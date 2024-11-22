<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ErrorCode;
use App\Http\Requests\NoLoginEmailVerificationRequest;
use Carbon\CarbonInterval;
use Config;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyEmailController
{
    /**
     * @throws Exception
     */
    public function showNotice(): Response
    {
        $emailExpirationInMinutes = Config::get('auth.verification.expire');
        $interval = CarbonInterval::minutes($emailExpirationInMinutes);
        $emailExpiration = $interval->cascade()->forHumans();

        return Inertia::render('Auth/VerifyEmailNoticePage', [
            'sendEmailVerificationUrl' => route('verification.send'),
            'emailVerificationExpiration' => $emailExpiration,
        ]);
    }

    public function verify(NoLoginEmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();
        return redirect()
            ->route('builder.resume.index')
            ->with('success', 'Email address verified.');
    }

    public function sendVerification(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->back()
                ->withErrors([ErrorCode::EMAIL_ALREADY_VERIFIED->value => 'Email address already verified.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return redirect()->back()->with('success', 'Verification email sent.');
    }
}
