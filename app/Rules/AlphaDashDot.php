<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class AlphaDashDot implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $errorMessage = 'The :attribute must only contain letters, numbers, dashes, underscores, and dots';
        if (is_null($value)) {
            $fail($errorMessage);
        }

        $formatIsValid = preg_match('/^[0-9A-Za-z_\-.]+$/u', $value) > 0;
        if (!$formatIsValid) {
            $fail($errorMessage);
        }
    }
}
