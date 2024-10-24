<?php

use App\Actions\User\CreateUserAction;
use App\DataTransferObjects\CreateUserDto;
use App\Enums\Gender;
use App\Enums\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\artisan;

beforeEach(function () {
    artisan('db:seed');
});

it('can create a user', /** @throws Throwable */ function () {
    $action = resolve(CreateUserAction::class);
    $gender = fake()->randomElement(Gender::toArray());
    $userDTO = new CreateUserDto(
        email: fake()->unique()->safeEmail(),
        username: fake()->unique()->userName(),
        password: fake()->password(8),
        first_name: fake()->firstName($gender),
        last_name: fake()->lastName(),
        middle_name: fake()->lastName(),
        roles: [Role::USER->value],
        active: true,
        email_verified: true,
        mobile_number: fake()->phoneNumber(),
        gender: $gender,
        birthday: fake()->date(),
        profile_picture_path: fake()->filePath(),
        country_id: DB::table('countries')->first()->id,
        address_line_1: fake()->streetName(),
        address_line_2: fake()->streetName(),
        address_line_3: fake()->streetName(),
        city_municipality: fake()->city(),
        province_state_county: fake()->city(),
        postal_code: fake()->postcode(),
    );

    $createdUser = $action->execute($userDTO);
    expect($createdUser)
        ->toBeInstanceOf(App\Models\User::class)
        ->and($createdUser->email)->toBe($userDTO->email)
        ->and($createdUser->username)->toBe($userDTO->username)
        ->and($createdUser->active)->toBe($userDTO->active)
        ->and($createdUser->email_verified_at)->not->toBeNull()
        ->and(Hash::check($userDTO->password, $createdUser->password))->toBeTrue()
        ->and($createdUser->roles->pluck('name')->toArray())->toBe($userDTO->roles)
        ->and($createdUser->userProfile->first_name)->toBe($userDTO->first_name)
        ->and($createdUser->userProfile->last_name)->toBe($userDTO->last_name)
        ->and($createdUser->userProfile->middle_name)->toBe($userDTO->middle_name)
        ->and($createdUser->userProfile->mobile_number)->toBe($userDTO->mobile_number)
        ->and($createdUser->userProfile->birthday->toDateString())->toBe($userDTO->birthday)
        ->and($createdUser->userProfile->gender->value)->toBe($userDTO->gender)
        ->and($createdUser->userProfile->address_line_1)->toBe($userDTO->address_line_1)
        ->and($createdUser->userProfile->address_line_2)->toBe($userDTO->address_line_2)
        ->and($createdUser->userProfile->address_line_3)->toBe($userDTO->address_line_3)
        ->and($createdUser->userProfile->city_municipality)->toBe($userDTO->city_municipality)
        ->and($createdUser->userProfile->province_state_county)->toBe($userDTO->province_state_county)
        ->and($createdUser->userProfile->postal_code)->toBe($userDTO->postal_code)
        ->and($createdUser->userProfile->country_id)->toBe($userDTO->country_id)
        ->and($createdUser->userProfile->profile_picture_path)->toBe($userDTO->profile_picture_path);
});
