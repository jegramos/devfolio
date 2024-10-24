<?php

namespace App\Actions\User;

use App\DataTransferObjects\CreateUserDto;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * This action handles the creation of a new user, including authentication data (`users` table),
 * user profile fields (`user_profiles` table), and role assignments. It performs all operations within a
 * database transaction to ensure data integrity.
 */
final readonly class CreateUserAction
{
    /**
     * @throws Throwable
     */
    public function execute(CreateUserDto $newUserDTO): User
    {
        return DB::transaction(function () use ($newUserDTO) {
            $authData = [
                'email' => $newUserDTO->email,
                'username' => $newUserDTO->username,
                'password' => $newUserDTO->password,
                'active' => $newUserDTO->active,
                'email_verified_at' => $newUserDTO->email_verified ? now()->toDateString() : null,
            ];
            $user = User::query()->create($authData);

            $exemptedAttributes = [
                'email',
                'username',
                'password',
                'active',
                'email_verified_at',
            ];
            $user->userProfile()->create(Arr::except($newUserDTO->toArray(), $exemptedAttributes));

            $userRoles = empty($newUserDTO->roles) ? [Role::USER] : $newUserDTO->roles;
            $user->assignRole($userRoles);

            return $user->load(['userProfile', 'roles']);
        });
    }
}
