<?php

namespace App\Console\Commands;

use App\Actions\Users\CreateUserAction;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Throwable;

class CreateUser extends Command
{
    protected $signature = 'user:create';

    protected $description = 'Create a new user';

    public function handle(CreateUserAction $createUserAction, Validator $validator): int
    {
        $firstName = $this->ask('First Name');
        $lastName = $this->ask('Last Name');
        $email = $this->ask('Email');
        $username = $this->ask('Username');
        $password = $this->secret('Password');
        $passwordConfirmation = $this->secret('Confirm Password');
        $role = $this->choice('Role', $this->getAllRoles());
        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'roles' => [Role::findByName($role)->id],
            'email_verified' => true,
        ];
        $createUserRules = (new UserRequest)->getStoreUserRules();

        try {
            $validator::validate($data, $createUserRules);
        } catch (ValidationException $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }

        try {
            $user = $createUserAction->execute(new User, $data);
        } catch (Throwable $th) {
            $this->error('Error: '.$th->getMessage());

            return Command::FAILURE;
        }

        $roles = $user->getRoleNames()->implode(', ');
        $this->info("User created: #$user->id | $user->email | {$user->userProfile->full_name} | $roles");

        return Command::SUCCESS;
    }

    /**
     * Get all roles in the database
     */
    private function getAllRoles(): array
    {
        $roles = [];
        Role::all()->each(function (Role $role) use (&$roles) {
            $roles[$role->id] = $role->name;
        });

        return $roles;
    }
}
