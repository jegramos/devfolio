<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUserAction;
use App\Http\Requests\RegisterRequest;
use DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class RegisterController
{
    public function showForm(): Response
    {
        $recaptchaEnabled = config('services.google.recaptcha.enabled');
        $checkAvailabilityBaseUrl = route('api.checkAvailability', ['type' => 1, 'value' => 1]);
        $checkAvailabilityBaseUrl = explode('/1/1', $checkAvailabilityBaseUrl)[0];

        return Inertia::render('Auth/RegisterPage', [
            'loginUrl' => route('auth.login.showForm'),
            'processRegistrationUrl' => route('auth.register.process'),
            'checkAvailabilityBaseUrl' => $checkAvailabilityBaseUrl,
            'countryOptions' => DB::table('countries')->select('id', 'name')->get(),
            'recaptchaEnabled' => $recaptchaEnabled,
            'recaptchaSiteKey' => $recaptchaEnabled ? config('services.google.recaptcha.site_key') : null,
        ]);
    }

    /**
     * Create and log-in the created user.
     *
     * @throws Throwable
     */
    public function process(RegisterRequest $request, CreateUserAction $createUserAction): RedirectResponse
    {
        $userInfo = [
            'email' => $request->validated('email'),
            'username' => $request->validated('username'),
            'password' => $request->validated('password'),
            'first_name' => $request->validated('first_name'),
            'last_name' => $request->validated('last_name'),
        ];

        $user = $createUserAction->execute($userInfo);
        auth()->login($user);
        $request->session()->regenerate();

        Event::dispatch(new Registered($user));

        return redirect()->route('builder.resume.index');
    }
}
