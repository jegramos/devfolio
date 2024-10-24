<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUserAction;
use App\DataTransferObjects\CreateUserDto;
use App\Http\Requests\RegisterRequest;
use DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class RegisterController
{
    public function showForm(): Response
    {
        $recaptchaEnabled = config('services.google_recaptcha.enabled');
        return Inertia::render('Auth/RegisterPage', [
            'loginUrl' => route('auth.login.showForm'),
            'processRegistrationUrl' => route('auth.register.processRegistration'),
            'countryOptions' => DB::table('countries')->select('id', 'name')->get(),
            'recaptchaEnabled' => $recaptchaEnabled,
            'recaptchaSiteKey' => $recaptchaEnabled ? config('services.google_recaptcha.site_key') : null,
        ]);
    }

    /**
     * Create and log-in the created user. A browser session can
     * only register once every minute
     *
     * @throws Throwable
     */
    public function processRegistration(RegisterRequest $request, CreateUserAction $createUserAction): RedirectResponse
    {
        $userDto = new CreateUserDto(
            email: $request->validated('email'),
            username: $request->validated('username'),
            password: $request->validated('password'),
            first_name: $request->validated('first_name'),
            last_name: $request->validated('last_name'),
            email_verified: false,
        );

        $user = $createUserAction->execute($userDto);
        Auth::login($user);
        $request->session()->regenerate();

        Event::dispatch(new Registered($user));

        return redirect()->route('builder.resume.index');
    }
}
