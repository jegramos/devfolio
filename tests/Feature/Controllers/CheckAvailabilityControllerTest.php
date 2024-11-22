<?php

use App\Enums\ErrorCode;
use Database\Factories\UserFactory;
use Database\Factories\UserProfileFactory;

use function Pest\Laravel\get;
use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    artisan('db:seed');
});

it('can check un-available email and username', function (string $type, string $value) {
    $username = $type === 'username' ? $value : fake()->unique()->userName();
    $email = $type === 'email' ? $value : fake()->unique()->safeEmail();
    $password = fake()->password(10);

    UserFactory::new()
        ->has(UserProfileFactory::new())
        ->create(['username' => $username, 'email' => $email, 'password' => $password]);

    $response = get(
        route('api.checkAvailability', ['type' => $type, 'value' => $value]),
        ['Accept' => 'application/json']
    );
    $response->assertOk();

    $response = json_decode($response->getContent(), true);
    assertFalse($response['available']);
})->with([
    'with username' => ['type' => 'username', 'value' => fake()->unique()->userName()],
    'with email' => ['type' => 'email', 'value' => fake()->unique()->safeEmail()],
]);

it('can check available username and email', function (string $type, string $value) {
    $username = fake()->unique()->userName();
    $email = fake()->unique()->safeEmail();
    $password = fake()->password(10);

    UserFactory::new()
        ->has(UserProfileFactory::new())
        ->create(['username' => $username, 'email' => $email, 'password' => $password]);

    $response = get(
        route('api.checkAvailability', ['type' => $type, 'value' => $value]),
        ['Accept' => 'application/json']
    );
    $response->assertOk();

    $response = json_decode($response->getContent(), true);
    assertTrue($response['available']);
})->with([
    'with username' => ['type' => 'username', 'value' => fake()->unique()->userName()],
    'with email' => ['type' => 'email', 'value' => fake()->unique()->safeEmail()],
]);

it('sets the response Content-Type to "application/json" implicitly', function () {
    $username = fake()->unique()->userName();
    $email = fake()->unique()->safeEmail();
    $password = fake()->password(10);

    UserFactory::new()
        ->has(UserProfileFactory::new())
        ->create(['username' => $username, 'email' => $email, 'password' => $password]);

    $response = get(route('api.checkAvailability', ['type' => 'email', 'value' => $email]));
    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/json');
});

it('ignores the ID of the user when `excludedId` param is provided', function (string $type, string $value) {
    $username = $type === 'username' ? $value : fake()->unique()->userName();
    $email = $type === 'email' ? $value : fake()->unique()->safeEmail();
    $password = fake()->password(10);

    $user = UserFactory::new()
        ->has(UserProfileFactory::new())
        ->create(['username' => $username, 'email' => $email, 'password' => $password]);

    $response = get(
        route('api.checkAvailability', ['type' => $type, 'value' => $value, 'excludedId' => $user->id]),
        ['Accept' => 'application/json']
    );
    $response->assertOk();

    $response = json_decode($response->getContent(), true);
    assertTrue($response['available']);
})->with([
    'with username' => ['type' => 'username', 'value' => fake()->unique()->userName()],
    'with email' => ['type' => 'email', 'value' => fake()->unique()->safeEmail()],
]);

it('only accepts "username" or "email" as the `type` param', function () {
    $type = 'non-existing-type';
    $response = get(
        route('api.checkAvailability', ['type' => $type, 'value' => 'test']),
        ['Accept' => 'application/json']
    );

    $response->assertNotFound();
    $response = json_decode($response->getContent(), true);
    assertEquals($response['error_code'], ErrorCode::UNKNOWN_ROUTE->value);
    assertFalse($response['success']);
});
