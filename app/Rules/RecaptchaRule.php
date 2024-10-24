<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     * @throws ConnectionException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!config('services.google_recaptcha.enabled')) {
            return;
        }

        if (empty($value)) {
            $fail('Please confirm that you are not a robot.');
        }

        $endpoint = Config::get('services.google_recaptcha.url');
        $secret = Config::get('services.google_recaptcha.secret_key');
        $response = Http::asForm()
            ->post($endpoint, [
                'secret' => $secret,
                'response' => $value,
            ])
            ->json();

        if (!isset($response['success']) || !$response['success']) {
            $fail('The reCAPTCHA failed.');
        }
    }
}
