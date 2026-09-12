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

    public function update(\App\Http\Requests\Teacher\UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        $request->session()->regenerate();

        return to_route('teacher.account.password.edit')
            ->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}
