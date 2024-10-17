<?php

namespace App\Models;

use App\Actions\AddSoftDeleteMarkerAction;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

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

                // Delete the UserProfile associated with the user
                $user->userProfile()->delete();
            });
        });
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }
}
