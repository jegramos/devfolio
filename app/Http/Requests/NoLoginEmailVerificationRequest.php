<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/**
 * This class extends the built-in Illuminate\Foundation\Auth\EmailVerificationRequest class.
 *
 * The modification in this class allows users to verify their email addresses without needing to log in.
 *
 * Example Scenario: A user registers using their laptop and then verifies their email using the
 * verification link received in the mailbox on their mobile phone.
 */
class NoLoginEmailVerificationRequest extends EmailVerificationRequest
{
    public function authorize(): bool
    {
        $this->resolveUser();

        if (! hash_equals((string) $this->user()->getKey(), (string) $this->route('id'))) {
            return false;
        }

        if (! hash_equals(sha1($this->user()->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }

    /**
     * Resolves the user by ID from the route and binds it to the request
     */
    private function resolveUser(): void
    {
        // Check if there is an authenticated user
        if ($this->user()) {
            return;
        }

        $user = User::findOrFail($this->route('id'));
        $this->setUserResolver(fn () => $user);
    }
}
