<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUserAction;
use App\Actions\User\SyncExternalAccountAction;
use App\Actions\User\UpdateUserAction;
use App\Enums\ErrorCode;
use App\Enums\ExternalLoginProvider;
use App\Enums\Role;
use App\Exceptions\DuplicateEmailException;
use Illuminate\Http\RedirectResponse;
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

        $user = $this->syncExternalAccountAction->execute(
            ExternalLoginProvider::GOOGLE,
            $provider->user(),
            $this->createUserAction,
            $this->updateUserAction,
            [Role::USER]
        );

        try {
            $user = $this->syncExternalAccountAction->execute(
                ExternalLoginProvider::GITHUB,
                $provider->user(),
                $this->createUserAction,
                $this->updateUserAction,
                [Role::USER]
            );
        } catch (DuplicateEmailException) {
            return redirect(route('auth.login.showForm'))->withErrors([
                ErrorCode::EXTERNAL_ACCOUNT_EMAIL_CONFLICT->value => 'You already have an account with that Github email address.'
            ]);
        }

        auth()->login($user);
        session()->regenerate();
        return redirect()->route('builder.resume.index');
    }
}
