<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Rules\AlphaDashDot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        return match ($routeName) {
            'users.store' => $this->getStoreUserRules(),
            default => [],
        };
    }

    public function getStoreUserRules(): array
    {
        return [
            'email' => ['email', 'required', 'unique:users,email'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', new AlphaDashDot],
            'password' => [
                'string',
                'required',
                'confirmed',
                'max:100',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'first_name' => ['string', 'required', 'max:255'],
            'last_name' => ['string', 'required', 'max:255'],
            'middle_name' => ['string', 'nullable', 'max:255'],
            'mobile_number' => [
                'nullable',
                'unique:user_profiles,mobile_number',
                'phone:mobile,lenient,international',
            ],
            'sex' => [new Enum(Gender::class), 'nullable'],
            'birthday' => ['date_format:Y-m-d', 'nullable', 'before_or_equal:'.$this->dateToday],
            'country_id' => ['string', 'nullable', 'exists:countries,id'],
            'address_line_1' => ['string', 'nullable', 'max:255'],
            'address_line_2' => ['string', 'nullable', 'max:255'],
            'address_line_3' => ['string', 'nullable', 'max:255'],
            'city_municipality' => ['string', 'nullable', 'max:255'],
            'province_state_county' => ['string', 'nullable', 'max:255'],
            'postal_code' => ['string', 'nullable', 'max:255'],
            'active' => ['boolean', 'nullable'],
            'email_verified' => ['boolean', 'nullable'],
            'profile_picture_path' => ['string', 'nullable', 'max:255'],
            'roles' => ['array', 'nullable'],
            'roles.*' => ['required', 'exists:roles,id', 'distinct'],
        ];
    }
}
