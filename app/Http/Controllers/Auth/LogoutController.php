<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LogoutRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController
{
    public function logoutCurrent(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('landingPage')
            ->with('message', 'You have been logged out.');
    }

    /**
     * @throws AuthenticationException
     */
    public function logoutOtherDevices(LogoutRequest $request): RedirectResponse
    {
        $password = $request->input('password');
        auth()->logoutOtherDevices($password);

        return redirect()->back();
    }
}
