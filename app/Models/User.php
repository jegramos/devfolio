<?php

namespace App\Models;

use App\Actions\AddSoftDeleteMarkerAction;
use App\Notifications\QueuedVerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'email',
        'username',
        'password',
        'active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'active' => 'boolean',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (User $user, AddSoftDeleteMarkerAction $addSoftDeleteMarkerAction) {
            DB::transaction(function () use ($user, $addSoftDeleteMarkerAction) {
                $user->email = $addSoftDeleteMarkerAction->execute($user->email);
                $user->saveQuietly();

                // Delete the UserProfile and ExternalAccount associated with the user
                $user->userProfile()->delete();
                $user->externalAccount()->delete();
            });
        });
    }

    /**
     * The username attribute should always be in lowercase.
     */
    protected function username(): Attribute
    {
        return Attribute::set(
            fn (string $value) => strtolower($value)
        );
    }

    /**
     * The email attribute should always be in lowercase.
     */
    protected function email(): Attribute
    {
        return Attribute::set(
            fn (string $value) => strtolower($value)
        );
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function externalAccount(): HasOne
    {
        return $this->hasOne(ExternalAccount::class);
    }

    public function isFromExternalAccount(): bool
    {
        return is_null($this->password);
    }

    /**
     * Send an email verification notification asynchronously
     */
    public function sendEmailVerificationNotification(): void
    {
        $expirationInMinutes = Config::get('auth.verification.expire', 60);
        $this->notify(new QueuedVerifyEmailNotification($this, $expirationInMinutes));
    }
}
