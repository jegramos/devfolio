<?php

namespace App\Http\Requests;

use App\Rules\DbVarcharMaxLengthRule;
use App\Rules\EmailRule;
use App\Rules\PasswordRule;
use App\Rules\RecaptchaRule;
use App\Rules\UsernameRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        $route = $this->route()->getName();

        return match ($route) {
            'auth.register.processRegistration' => $this->getProcessRegistrationRules(),
            default => [],
        };
    }

    private function getProcessRegistrationRules(): array
    {
        return [
            'email' => ['required', new EmailRule()],
            'username' => ['required', new UsernameRule()],
            'password' => ['required', 'confirmed', new PasswordRule()],
            'first_name' => ['required', new DbVarcharMaxLengthRule()],
            'last_name' => ['required', new DbVarcharMaxLengthRule()],
            'country_id' => ['required', 'exists:countries,id'],
            'recaptcha_response_token' => [new RecaptchaRule()]
        ];
    }

    public function messages(): array
    {
        return [
            'country_id.required' => 'The country field is required.',
            'country_id.exists' => 'Select a valid country.',
        ];
    }
}
