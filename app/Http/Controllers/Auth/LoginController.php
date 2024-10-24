<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ErrorCode;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LoginController
{
    public function showForm(): Response
    {
        return Inertia::render('Auth/LoginPage', [
            'registerUrl' => route('auth.register.showForm'),
            'authenticateUrl' => route('auth.login.authenticate'),
        ]);
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        // The user can provide their email or username
        $credentials = !$request->input('email')
            ? $request->only('username', 'password')
            : $request->only('email', 'password');

        // The user must be active for them to login
        $credentials['active'] = true;

        if (auth()->attempt($credentials, $request->input('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('builder.resume.index'));
        }

        return redirect()->back()->withErrors([
            ErrorCode::INVALID_CREDENTIALS->value => 'The provided credentials do not match our records.',
        ]);
    }
}
