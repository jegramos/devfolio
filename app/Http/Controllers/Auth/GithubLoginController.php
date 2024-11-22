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
use Laravel\Socialite\Two\GithubProvider;
use Throwable;

class GithubLoginController
{
    public function __construct(
        private CreateUserAction $createUserAction,
        private UpdateUserAction $updateUserAction,
        private SyncExternalAccountAction $syncExternalAccountAction
    ) {
    }

    public function redirect(): RedirectResponse
    {
        $config = config('services.github.oauth');
        $provider = Socialite::buildProvider(GithubProvider::class, $config);
        return $provider->redirect();
    }


    /**
     * @throws Throwable
     */
    public function callback(): RedirectResponse
    {
        $config = config('services.github.oauth');
        $provider = Socialite::buildProvider(GithubProvider::class, $config);

        try {
            $user = $this->syncExternalAccountAction->execute(
                ExternalLoginProvider::GITHUB,
                $provider->user(),
                $this->createUserAction,
                $this->updateUserAction,
                [Role::USER]
            );
        } catch (DuplicateEmailException) {
            $message = 'An account with your Github email address already exists.';
            return redirect(route('auth.login.showForm'))->withErrors([
                ErrorCode::EXTERNAL_ACCOUNT_EMAIL_CONFLICT->value => $message
            ]);
        }

        auth()->login($user);
        session()->regenerate();
        return redirect()
            ->route('builder.resume.index')
            ->with('success', 'You have logged in via Github.');
    }
}
