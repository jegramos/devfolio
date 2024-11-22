<?php

namespace Database\Factories;

use App\Enums\ExternalLoginProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

class ExternalAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new()->has(UserProfileFactory::new()),
            'provider' => fake()->randomElement(ExternalLoginProvider::toArray()),
            'provider_id' => fake()->randomNumber(5),
            'access_token' => Crypt::encrypt(fake()->uuid),
            'refresh_token' => Crypt::encrypt(fake()->uuid),
            'id_token' => Crypt::encrypt(fake()->uuid),
        ];
    }
}
