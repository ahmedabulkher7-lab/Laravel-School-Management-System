<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(): View
    {
        return view('teacher.account.password');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'أدخل كلمة المرور الحالية.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.required' => 'أدخل كلمة المرور الجديدة.',
            'password.min' => 'يجب أن تتكون كلمة المرور الجديدة من 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور الجديدة غير متطابق.',
            'password.different' => 'اختر كلمة مرور جديدة مختلفة عن الحالية.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        $request->session()->regenerate();

        return to_route('teacher.account.password.edit')
            ->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}
