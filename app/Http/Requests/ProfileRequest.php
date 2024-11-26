<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Rules\DbVarcharMaxLengthRule;
use App\Rules\InternationalPhoneFormatRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        $route = $this->route()->getName();

        return match ($route) {
            'profile.update' => $this->getUpdateProfileRules(),
            default => [],
        };
    }

    private function getUpdateProfileRules(): array
    {
        return [
            'given_name' => ['nullable', 'string', new DbVarcharMaxLengthRule()],
            'family_name' => ['nullable', 'string', new DbVarcharMaxLengthRule()],
            'mobile_number' => [
                'nullable',
                new InternationalPhoneFormatRule(),
                'phone:mobile,lenient,international',
                'unique:user_profiles,mobile_number,' . $this->user()->id . ',user_id',
            ],
            'gender' => ['nullable', new Enum(Gender::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . date('Y-m-d')],
            'country_id' => ['nullable', 'exists:countries,id'],
            'address_line_1' => ['nullable', new DbVarcharMaxLengthRule()],
            'address_line_2' => ['nullable', new DbVarcharMaxLengthRule()],
            'address_line_3' => ['nullable', new DbVarcharMaxLengthRule()],
            'city_municipality' => ['nullable', new DbVarcharMaxLengthRule()],
            'province_state_county' => ['nullable', new DbVarcharMaxLengthRule()],
            'postal_code' => ['nullable', new DbVarcharMaxLengthRule()],
        ];
    }

    public function messages(): array
    {
        return [
            /** @see https://github.com/Propaganistas/Laravel-Phone#validation */
            'mobile_number.phone' => 'The :attribute field format must be a valid mobile number',
        ];
    }
}
