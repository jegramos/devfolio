<?php

namespace App\Models;

use App\Actions\AddSoftDeleteMarkerAction;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'mobile_number',
        'gender',
        'birthday',
        'profile_picture_path',
        'country_id',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city_municipality',
        'province_state_county',
        'postal_code',
    ];

    protected $casts = [
        'birthday' => 'date:Y-m-d',
        'gender' => Gender::class,
    ];

    protected $appends = [
        /** @uses fullName() */
        'full_name',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (UserProfile $userProfile, AddSoftDeleteMarkerAction $addSoftDeleteMarkerAction) {
            if ($userProfile->mobile_number) {
                $userProfile->mobile_number = $addSoftDeleteMarkerAction->execute(
                    $userProfile->mobile_number
                );

                $userProfile->saveQuietly();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $firstName = $this->first_name;
            $lastName = $this->last_name;
            $middleName = $this->middle_name;

            return $middleName ? "$firstName $middleName $lastName" : "$firstName $lastName";
        });
    }
}
