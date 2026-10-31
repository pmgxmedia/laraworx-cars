<?php

namespace Database\Factories;

use App\Models\PrivateSeller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PrivateSeller>
 */
class PrivateSellerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ulid' => (string) Str::ulid(),
            'user_id' => 1,
            'contact_email' => fake()->safeEmail(),
            'phone' => fake()->numerify('0#########'),
            'city' => fake()->randomElement(['Johannesburg', 'Cape Town', 'Durban', 'Pretoria']),
            'province' => fake()->randomElement(['Gauteng', 'Western Cape', 'KwaZulu-Natal']),
            'status' => PrivateSeller::STATUS_VERIFIED,
            'verified_at' => now()->subDays(fake()->numberBetween(5, 100)),
            'is_active' => true,
        ];
    }
}