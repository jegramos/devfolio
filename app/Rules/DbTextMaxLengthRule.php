<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\PotentiallyTranslatedString;

class DbTextMaxLengthRule implements ValidationRule
{
    /** @see https://stackoverflow.com/questions/6766781/maximum-length-for-mysql-type-text */
    private const DB_TEXT_MAX_LENGTH = 65535;

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => ['string', 'max:' . static::DB_TEXT_MAX_LENGTH]]
        );

        if ($validator->fails()) {
            $fail($validator->errors()->first($attribute));
        }
    }
}
