<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovedGraduate;
use App\Models\Graduate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GraduatePasswordController extends Controller
{
    public function update(Request $request, ApprovedGraduate $approvedGraduate)
    {
        if (!in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            abort(403);
        }

        $graduate = Graduate::where('university_id', $approvedGraduate->university_id)->first();

        if (!$graduate || !$graduate->user) {
            return back()->with('error', 'لا يوجد حساب مستخدم مرتبط بهذا الخريج، لذلك لا يمكن تغيير كلمة المرور.');
        }

        $user = $graduate->user;

        if ($user->role !== 'graduate') {
            return back()->with('error', 'الحساب المرتبط بهذا السجل ليس حساب خريج، وتم إلغاء العملية.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->remember_token = Str::random(60);
        $user->save();

        return back()->with('success', 'تم تغيير كلمة مرور الخريج بنجاح، ويمكنه الآن تسجيل الدخول باستخدام كلمة المرور الجديدة.');
    }
}
