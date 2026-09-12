<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(\App\Http\Requests\Auth\LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->hasRole('admin'))   return redirect()->route('admin.dashboard');
            if ($user->hasRole('teacher')) return redirect()->route('teacher.dashboard');
            if ($user->hasRole('student')) return redirect()->route('student.dashboard');
        }

        return back()
            ->withErrors(['email' => 'بيانات الدخول غير صحيحة'])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
