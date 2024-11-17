<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Rules\DbVarcharMaxLengthRule;
use App\Rules\EmailRule;
use App\Rules\PasswordRule;
use App\Rules\UsernameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'email' => ['required', new EmailRule()],
            'username' => ['required', new UsernameRule()],
            'password' => [
                'required',
                'confirmed',
                new PasswordRule(),
            ],
            'first_name' => ['required', new DbVarcharMaxLengthRule()],
            'last_name' => ['required', new DbVarcharMaxLengthRule()],
            'middle_name' => ['nullable', new DbVarcharMaxLengthRule()],
            'mobile_number' => [
                'nullable',
                'unique:user_profiles,mobile_number',
                'phone:mobile,lenient,international',
            ],
            'sex' => [new Enum(Gender::class), 'nullable'],
            'birthday' => ['date_format:Y-m-d', 'nullable', 'before_or_equal:' . date('Y-m-d')],
            'country_id' => ['nullable', 'string', 'exists:countries,id'],
            'address_line_1' => ['nullable', new DbVarcharMaxLengthRule()],
            'address_line_2' => ['nullable', new DbVarcharMaxLengthRule()],
            'address_line_3' => ['nullable', new DbVarcharMaxLengthRule()],
            'city_municipality' => ['nullable', new DbVarcharMaxLengthRule()],
            'province_state_county' => ['nullable', new DbVarcharMaxLengthRule()],
            'postal_code' => ['nullable', new DbVarcharMaxLengthRule()],
            'active' => ['required', 'boolean'],
            'email_verified_at' => ['nullable', 'date'],
            'profile_picture_path' => ['nullable', new DbVarcharMaxLengthRule()],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'distinct', 'exists:roles,name'],
        ];
    }
}
