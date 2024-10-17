<?php

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 *
 * This action handles the creation of a new user, including authentication data (`users` table),
 * user profile fields (`user_profiles` table), and role assignments. It performs all operations within a
 * database transaction to ensure data integrity.
 *
 */
final readonly class CreateUserAction
{
    /** @throws Throwable */
    public function execute(Model $model, array $info): User
    {
        return DB::transaction(function () use ($model, $info) {
            $authData = [
                'email' => $info['email'],
                'username' => $info['username'],
                'password' => $info['password'],
            ];

            if (isset($userInfo['active'])) {
                $authData['active'] = $info['active'];
            }

            if (isset($userInfo['email_verified'])) {
                $authData['email_verified_at'] = $info['email_verified'] ? Carbon::now() : null;
            }

            /** @var User $user */
            $user = $model::query()->create($authData);

            // Set the user profile fields
            $exemptedAttributes = [
                'email',
                'username',
                'password',
                'active',
                'email_verified_at',
            ];
            $user->userProfile()->create(Arr::except($info, $exemptedAttributes));

            // Set the Roles
            $userRoles = empty($userInfo['roles']) ? [Role::USER] : $userInfo['roles'];
            $user->assignRole($userRoles);

            return $user;
        });
    }
}
