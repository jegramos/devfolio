<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the availability check for a given type and value.
 *
 * This Controller checks if a user already exists with the given type (email, username, or mobile_number)
 * and value. It optionally allows excluding a specific user ID from the check.
 */
class CheckAvailabilityController extends ApiController
{
    /**
     * @throws ValidationException
     */
    public function __invoke(string $type, string $value, Request $request, ?int $excludedId = null): JsonResponse
    {
        $validator = Validator::make(['value' => $value], [
            'value' => ['required', $type === 'email' ? 'email' : 'string']
        ]);

        $validator->validate();

        $query = User::query();
        if (in_array($type, ['email', 'username'])) {
            $query->when($type === 'email', fn ($query) => $query->where($type, $value))
                ->when($type === 'username', fn ($query) => $query->where($type, $value))
                ->when(!is_null($excludedId), fn ($query) => $query->whereNot('id', $excludedId));
        } else {
            $query = UserProfile::query()
                ->where($type, $value)
                ->when(!is_null($excludedId), fn ($query) => $query->whereNot('user_id', $excludedId));
        }

        $found = $query->exists();
        return $this->success(['available' => !$found], Response::HTTP_OK);
    }
}
