<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /** Valid admin roles */
    private const ADMIN_ROLES = ['admin', 'super_admin', 'academic_admin', 'finance_admin', 'employment_officer'];

    /** List all admin users */
    public function index()
    {
        $admins = User::whereIn('role', self::ADMIN_ROLES)
            ->latest()
            ->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    /** Show create form */
    public function create()
    {
        $roles = self::ADMIN_ROLES;
        return view('admin.admins.create', compact('roles'));
    }

    /** Store a new admin */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['required', Rule::in(self::ADMIN_ROLES)],
            'is_active'=> ['required', 'boolean'],
            'signer_role' => ['nullable', 'string', 'max:255'],
        ]);

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'is_active' => (bool) $validated['is_active'],
            'signer_role' => $validated['signer_role'] ?? null,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', __('app.admin_created_success'));
    }

    /** Show edit form */
    public function edit(User $admin)
    {
        abort_unless(in_array($admin->role, self::ADMIN_ROLES), 404);
        $roles = self::ADMIN_ROLES;
        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    /** Update an existing admin */
    public function update(Request $request, User $admin)
    {
        abort_unless(in_array($admin->role, self::ADMIN_ROLES), 404);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'password'  => ['nullable', 'confirmed', Password::min(8)],
            'role'      => ['required', Rule::in(self::ADMIN_ROLES)],
            'is_active' => ['required', 'boolean'],
            'signer_role' => ['nullable', 'string', 'max:255'],
        ]);

        // Safety: Cannot deactivate the last remaining active admin
        if (! $validated['is_active'] && $admin->is_active) {
            $activeAdminCount = User::whereIn('role', self::ADMIN_ROLES)
                ->where('is_active', true)
                ->count();

            if ($activeAdminCount <= 1) {
                return back()
                    ->with('error', __('app.admin_last_active_error'))
                    ->withInput();
            }
        }

        $data = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'is_active' => (bool) $validated['is_active'],
            'signer_role' => $validated['signer_role'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', __('app.admin_updated_success'));
    }

    /** Delete an admin */
    public function destroy(User $admin)
    {
        abort_unless(in_array($admin->role, self::ADMIN_ROLES), 404);

        // Safety: Cannot delete if this is the only admin account left
        $adminCount = User::whereIn('role', self::ADMIN_ROLES)->count();
        if ($adminCount <= 1) {
            return back()->with('error', __('app.admin_last_delete_error'));
        }

        // Safety: Cannot delete yourself
        if ($admin->id === auth()->id()) {
            return back()->with('error', __('app.admin_self_delete_error'));
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', __('app.admin_deleted_success'));
    }

    /** Toggle active/inactive status quickly */
    public function toggleStatus(User $admin)
    {
        abort_unless(in_array($admin->role, self::ADMIN_ROLES), 404);

        // Safety: Cannot deactivate yourself
        if ($admin->id === auth()->id()) {
            return back()->with('error', __('app.admin_self_deactivate_error'));
        }

        // Safety: Cannot deactivate the last active admin
        if ($admin->is_active) {
            $activeCount = User::whereIn('role', self::ADMIN_ROLES)
                ->where('is_active', true)
                ->count();
            if ($activeCount <= 1) {
                return back()->with('error', __('app.admin_last_active_error'));
            }
        }

        $admin->update(['is_active' => ! $admin->is_active]);

        $msg = $admin->is_active
            ? __('app.admin_activated_success')
            : __('app.admin_deactivated_success');

        return back()->with('success', $msg);
    }
}
