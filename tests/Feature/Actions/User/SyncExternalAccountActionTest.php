<?php

use App\Actions\User\CreateUserAction;
use App\Actions\User\SyncExternalAccountAction;
use App\Actions\User\UpdateUserAction;
use App\Enums\ExternalLoginProvider;
use App\Enums\Role;
use App\Exceptions\DuplicateEmailException;
use App\Models\ExternalAccount;
use App\Models\User;
use Database\Factories\ExternalAccountFactory;
use Database\Factories\UserFactory;
use Database\Factories\UserProfileFactory;
use Laravel\Socialite\Two\User as SocialiteUser;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    artisan('db:seed');
});

test('it can create a google new user', /** @throws Throwable */ function () {
    $providerUser = Mockery::mock(SocialiteUser::class);
    $providerUser
        ->shouldReceive('user')
        ->shouldReceive('getId')
        ->andReturn(rand(1, 100))
        ->shouldReceive('getEmail')
        ->andReturn('foo@example.com')
        ->shouldReceive('token')
        ->andReturn(Str::random(40))
        ->shouldReceive('refreshToken')
        ->andReturn(Str::random(40))
        ->shouldReceive('getAvatar')
        ->andReturn(fake()->imageUrl());

    $providerUser->user = [
        'given_name' => fake()->firstName(),
        'family_name' => fake()->lastName(),
    ];

    $syncAction = resolve(SyncExternalAccountAction::class);
    $createUserAction = resolve(CreateUserAction::class);
    $updateUserAction = resolve(UpdateUserAction::class);
    $syncAction->execute(
        ExternalLoginProvider::GOOGLE,
        $providerUser,
        $createUserAction,
        $updateUserAction,
        [Role::USER]
    );

    assertDatabaseCount('users', 1);
});

test('it can create a github new user', /** @throws Throwable */ function () {
    $providerUser = Mockery::mock(SocialiteUser::class);
    $providerUser
        ->shouldReceive('user')
        ->shouldReceive('getId')
        ->andReturn(rand(1, 100))
        ->shouldReceive('getEmail')
        ->andReturn('foo@example.com')
        ->shouldReceive('token')
        ->andReturn(Str::random(40))
        ->shouldReceive('refreshToken')
        ->andReturn(Str::random(40))
        ->shouldReceive('getAvatar')
        ->andReturn(fake()->imageUrl());

    $providerUser->user = [
        'name' => fake()->firstName() . ' ' . fake()->lastName(),
    ];

    $syncAction = resolve(SyncExternalAccountAction::class);
    $createUserAction = resolve(CreateUserAction::class);
    $updateUserAction = resolve(UpdateUserAction::class);
    $syncAction->execute(
        ExternalLoginProvider::GITHUB,
        $providerUser,
        $createUserAction,
        $updateUserAction,
        [Role::USER]
    );

    assertDatabaseCount('users', 1);
});

test('it can update an google existing user', /** @throws Throwable */ function () {
    // Create an existing user with an external account
    /** @var ExternalAccount $externalAccount */
    $userFactory = UserFactory::new()->has(UserProfileFactory::new());
    $externalAccount = ExternalAccountFactory::new()
        ->for($userFactory)
        ->create(['provider' => ExternalLoginProvider::GOOGLE])
        ->first();

    $providerUser = Mockery::mock(SocialiteUser::class);
    $providerUser
        ->shouldReceive('user')
        ->shouldReceive('getId')
        ->andReturn($externalAccount->provider_id)
        ->shouldReceive('getEmail')
        ->andReturn($externalAccount->user->email)
        ->shouldReceive('token')
        ->andReturn(Str::random(40))
        ->shouldReceive('refreshToken')
        ->andReturn(Str::random(40))
        ->shouldReceive('getAvatar')
        ->andReturn(fake()->imageUrl());

    $providerUser->user = [
        'given_name' => fake()->firstName(),
        'family_name' => fake()->lastName(),
    ];

    $syncAction = resolve(SyncExternalAccountAction::class);
    $createUserAction = resolve(CreateUserAction::class);
    $updateUserAction = resolve(UpdateUserAction::class);
    $updatedUser = $syncAction->execute(
        $externalAccount->provider,
        $providerUser,
        $createUserAction,
        $updateUserAction,
        [Role::USER]
    );

    expect($updatedUser)
        ->toBeInstanceOf(User::class)
        ->and($updatedUser->email)->toBe($externalAccount->user->email)
        ->and($updatedUser->externalAccount->access_token)->toBe($providerUser->token)
        ->and($updatedUser->externalAccount->refresh_token)->toBe($providerUser->refreshToken)
        ->and($updatedUser->refreshToken)->toBe($providerUser->refreshToken)
        ->and($updatedUser->userProfile->first_name)->toBe($providerUser->user['given_name'])
        ->and($updatedUser->userProfile->last_name)->toBe($providerUser->user['family_name'])
        ->and($updatedUser->userProfile->profile_picture_path)->toBe($providerUser->getAvatar())
        ->and($updatedUser->email_verified_at)->not()->toBeNull();

    assertDatabaseCount('users', 1);
});

test('it can update an github existing user', /** @throws Throwable */ function (string $name) {
    // Create an existing user with an external account
    /** @var ExternalAccount $externalAccount */
    $userFactory = UserFactory::new()->has(UserProfileFactory::new()->state(['middle_name' => null]))->create();
    $externalAccount = ExternalAccountFactory::new()
        ->for($userFactory)
        ->create(['provider' => ExternalLoginProvider::GITHUB])
        ->first();

    $providerUser = Mockery::mock(SocialiteUser::class);
    $providerUser
        ->shouldReceive('user')
        ->shouldReceive('getId')
        ->andReturn($externalAccount->provider_id)
        ->shouldReceive('getEmail')
        ->andReturn($externalAccount->user->email)
        ->shouldReceive('token')
        ->andReturn(Str::random(40))
        ->shouldReceive('refreshToken')
        ->andReturn(Str::random(40))
        ->shouldReceive('getAvatar')
        ->andReturn(fake()->imageUrl());

    $providerUser->user = [
        'name' => $name,
    ];

    $syncAction = resolve(SyncExternalAccountAction::class);
    $createUserAction = resolve(CreateUserAction::class);
    $updateUserAction = resolve(UpdateUserAction::class);
    $updatedUser = $syncAction->execute(
        $externalAccount->provider,
        $providerUser,
        $createUserAction,
        $updateUserAction,
        [Role::USER]
    );

    expect($updatedUser)
        ->toBeInstanceOf(User::class)
        ->and($updatedUser->email)->toBe($externalAccount->user->email)
        ->and($updatedUser->externalAccount->access_token)->toBe($providerUser->token)
        ->and($updatedUser->externalAccount->refresh_token)->toBe($providerUser->refreshToken)
        ->and($updatedUser->refreshToken)->toBe($providerUser->refreshToken)
        ->and($updatedUser->userProfile->full_name)->toBe($providerUser->user['name'])
        ->and($updatedUser->userProfile->profile_picture_path)->toBe($providerUser->getAvatar())
        ->and($updatedUser->email_verified_at)->not()->toBeNull();

    assertDatabaseCount('users', 1);
})->with([
    'with a 2-part name' => ['name' => 'Bill Gates'],
    'with a 3-part name' => ['name' => 'Kobe Bean Bryant'],
    'with a 4-part name' => ['name' => ' John Jacobs David Washington'],
]);

test('it can throw a duplicate email exception', /** @throws Throwable */ function () {
    // Create an existing user with an external account
    /** @var ExternalAccount $externalAccount */
    $userFactory = UserFactory::new()->has(UserProfileFactory::new());
    $externalAccount = ExternalAccountFactory::new()
        ->has($userFactory)
        ->create(['provider' => ExternalLoginProvider::GOOGLE])
        ->first();

    $providerUser = Mockery::mock(SocialiteUser::class);
    $providerUser
        ->shouldReceive('user')
        ->shouldReceive('getId')
        ->andReturn(Str::uuid()->toString()) // different external account
        ->shouldReceive('getEmail')
        ->andReturn($externalAccount->user->email) // with the same email as an existing internal user
        ->shouldReceive('token')
        ->andReturn(Str::random(40))
        ->shouldReceive('refreshToken')
        ->andReturn(Str::random(40))
        ->shouldReceive('getAvatar')
        ->andReturn(fake()->imageUrl());

    $providerUser->user = [
        'given_name' => fake()->firstName(),
        'family_name' => fake()->lastName(),
    ];

    $syncAction = resolve(SyncExternalAccountAction::class);
    $createUserAction = resolve(CreateUserAction::class);
    $updateUserAction = resolve(UpdateUserAction::class);
    $syncAction->execute(
        $externalAccount->provider,
        $providerUser,
        $createUserAction,
        $updateUserAction,
        [Role::USER]
    );
})->throws(DuplicateEmailException::class);
