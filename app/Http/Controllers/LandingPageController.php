<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            }

            if ($user->hasRole('student')) {
                return redirect()->route('student.dashboard');
            }
        }

        return view('welcome');
    }

    public function storeContact(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return redirect('/#contact')->with(
            'contact_success',
            'Thanks for reaching out! Our admissions team will be in touch shortly.'
        );
    }
}
