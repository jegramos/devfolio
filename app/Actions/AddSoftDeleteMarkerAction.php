<?php

namespace App\Actions;

/**
 * This action class handles the addition of a soft-delete marker to a given string value.
 *
 * Can be useful for marking unique database values when soft deleting, allowing those
 * values to be reused or preserved.
 *
 * Example:
 * <code>
 *   $email = 'test1@example.com';
 *   $addSoftDeleteMarkerAction = resolve(AddSoftDeleteMarkerAction::class);
 *   $emailWithMarker = $addSoftDeleteMarkerAction->execute($email);
 *   echo $emailWithMarker; // test1@example.com::deleted_1696335600
 * </code>
 */
readonly class AddSoftDeleteMarkerAction
{
    public function execute(string $value, string $separator = '::deleted_'): string
    {
        return $value . $separator . time();
    }
}
