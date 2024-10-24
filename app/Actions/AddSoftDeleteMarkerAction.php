<?php

namespace App\Actions;

/**
 * This action handles the addition of a soft-delete marker to a given string value.
 *
 * This can be useful for marking unique database values when soft deleting, allowing those
 * values to be reused or preserved.
 *
 * Example:
 * Given: test_email@gmail.com
 * Result: test_email@gmail.com::deleted_<timestamp>
 */
final readonly class AddSoftDeleteMarkerAction
{
    public function execute(string $value, string $separator = '::deleted_'): string
    {
        return $value . $separator . time();
    }
}
