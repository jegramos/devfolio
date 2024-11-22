<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\PotentiallyTranslatedString;

class DbVarcharMaxLengthRule implements ValidationRule
{
    private const DB_VARCHAR_MAX_LENGTH = 255;

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => ['string', 'max:' . static::DB_VARCHAR_MAX_LENGTH]]
        );

        if ($validator->fails()) {
            $fail($validator->errors()->first($attribute));
        }
    }
}
