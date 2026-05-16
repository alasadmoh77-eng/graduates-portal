<?php

namespace App\Http\Controllers;

use App\Http\Requests\GraduateProfileUpdateRequest;
use App\Models\Graduate;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GraduateProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user()->load(['graduate.major']);

        return view('graduate.profile.show', compact('user'));
    }

    public function edit(): View
    {
        $user = Auth::user()->load(['graduate.major']);
        $majors = Major::orderBy('name_ar')->get();

        return view('graduate.profile.edit', compact('user', 'majors'));
    }

    public function update(GraduateProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $graduate = $user->graduate;

        if (! $graduate) {
            abort(404);
        }

        DB::transaction(function () use ($request, $user, $graduate): void {
            $user->update($request->safe()->only(['name', 'email']));

            $data = $request->safe()->only(['phone', 'major_id', 'graduation_year']);

            if ($request->hasFile('photo')) {
                $this->deleteStoredPublicFile(Graduate::normalizeRelativePublicPath($graduate->photo));
                $data['photo'] = $request->file('photo')->store('profile-photos', 'public');
            }

            if ($request->hasFile('cv')) {
                $this->deleteStoredPublicFile(Graduate::normalizeRelativePublicPath($graduate->cv_path));
                $data['cv_path'] = $request->file('cv')->store('cvs', 'public');
            }

            $graduate->update($data);
        });

        return redirect()
            ->route('graduate.profile.show')
            ->with('success', __('app.profile_updated'));
    }

    /**
     * Stream the authenticated graduate's profile photo (avoids /storage symlink issues on Windows + php artisan serve).
     */
    public function showPhoto(): BinaryFileResponse
    {
        $graduate = Auth::user()->graduate;
        if (! $graduate) {
            abort(404);
        }

        $path = $graduate->photoRelativePath();
        if (! $path || ! str_starts_with($path, 'profile-photos/')) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $absolute = Storage::disk('public')->path($path);

        return response()->file($absolute, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function downloadCv(): RedirectResponse|StreamedResponse
    {
        $graduate = Auth::user()->graduate;
        $cvPath = $graduate?->cvRelativePath();
        if (! $cvPath) {
            return back()->with('error', __('app.profile_no_cv'));
        }

        if (! Storage::disk('public')->exists($cvPath)) {
            return back()->with('error', __('app.profile_cv_missing_file'));
        }

        return Storage::disk('public')->download($cvPath);
    }

    private function deleteStoredPublicFile(?string $normalizedRelativePath): void
    {
        if (! $normalizedRelativePath || ! $this->isAllowedPublicPath($normalizedRelativePath)) {
            return;
        }

        if (Storage::disk('public')->exists($normalizedRelativePath)) {
            Storage::disk('public')->delete($normalizedRelativePath);
        }
    }

    private function isAllowedPublicPath(string $path): bool
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        return str_starts_with($path, 'cvs/')
            || str_starts_with($path, 'profile-photos/');
    }
}
