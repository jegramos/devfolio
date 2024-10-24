<?php

namespace App\DataTransferObjects;

readonly class CreateUserDto
{
    public function __construct(
        public string $email,
        public string $username,
        public string $password,
        public string $first_name,
        public string $last_name,
        public ?string $middle_name = null,
        public array $roles = [],
        public bool $active = true,
        public bool $email_verified = true,
        public ?string $mobile_number = null,
        public ?string $gender = null,
        public ?string $birthday = null,
        public ?string $profile_picture_path = null,
        public ?int $country_id = null,
        public ?string $address_line_1 = null,
        public ?string $address_line_2 = null,
        public ?string $address_line_3 = null,
        public ?string $city_municipality = null,
        public ?string $province_state_county = null,
        public ?string $postal_code = null,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            $data['email'],
            $data['username'],
            $data['password'],
            $data['first_name'],
            $data['last_name'],
            $data['middle_name'] ?? null,
            $data['roles'] ?? [],
            $data['active'] ?? true,
            $data['email_verified'] ?? true,
            $data['mobile_number'] ?? null,
            $data['gender'] ?? null,
            $data['birthday'] ?? null,
            $data['profile_picture_path'] ?? null,
            $data['country_id'] ?? null,
            $data['address_line_1'] ?? null,
            $data['address_line_2'] ?? null,
            $data['address_line_3'] ?? null,
            $data['city_municipality'] ?? null,
            $data['province_state_county'] ?? null,
            $data['postal_code'] ?? null,
        );
    }

    public function toArray(): array
    {
        $properties = get_object_vars($this);

        $emailVerifiedAt = $properties['email_verified'] ? now()->toDateString() : null;
        unset($properties['email_verified']);
        $properties['email_verified_at'] = $emailVerifiedAt;

        return $properties;
    }
}
