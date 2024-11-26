<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUserAction;
use App\Actions\User\SyncExternalAccountAction;
use App\Actions\User\UpdateUserAction;
use App\Enums\ErrorCode;
use App\Enums\ExternalLoginProvider;
use App\Enums\Role;
use App\Enums\SessionFlashKey;
use App\Exceptions\DuplicateEmailException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Throwable;

class GoogleLoginController
{
    public function __construct(
        private CreateUserAction $createUserAction,
        private UpdateUserAction $updateUserAction,
        private SyncExternalAccountAction $syncExternalAccountAction
    ) {
    }

    public function redirect(): RedirectResponse
    {
        $config = config('services.google.oauth');
        $provider = Socialite::buildProvider(GoogleProvider::class, $config);
        return $provider->redirect();
    }

    /**
     * @throws DuplicateEmailException
     * @throws Throwable
     */
    public function callback(): RedirectResponse
    {
        $config = config('services.google.oauth');
        $provider = Socialite::buildProvider(GoogleProvider::class, $config);

        try {
            $user = $this->syncExternalAccountAction->execute(
                ExternalLoginProvider::GOOGLE,
                $provider->user(),
                $this->createUserAction,
                $this->updateUserAction,
                [Role::USER]
            );
        } catch (DuplicateEmailException) {
            $message = 'An account with your Google email address already exists.';
            return redirect(route('auth.register.showForm'))->withErrors([
                ErrorCode::EXTERNAL_ACCOUNT_EMAIL_CONFLICT->value => $message
            ]);
        }

        Auth::login($user);
        Session::regenerate();
        return redirect()
            ->route('builder.resume.index')
            ->with(SessionFlashKey::CMS_LOGIN_SUCCESS->value, 'You have logged in via Google.');
    }
}
