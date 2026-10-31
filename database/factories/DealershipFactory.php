<?php

namespace Database\Factories;

use App\Models\Dealership;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Dealership>
 */
class DealershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ulid' => (string) Str::ulid(),
            'business_name' => fake()->company().' Motors',
            'trading_name' => fake()->company(),
            'registration_number' => fake()->numerify('20####/######/07'),
            'vat_number' => fake()->numerify('4########'),
            'contact_person_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('0#########'),
            'website' => 'https://'.fake()->domainName(),
            'description' => fake()->paragraph(3),
            'street_address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Midrand', 'Sandton']),
            'province' => fake()->randomElement(['Gauteng', 'Western Cape', 'KwaZulu-Natal']),
            'postal_code' => fake()->numerify('####'),
            'status' => Dealership::STATUS_VERIFIED,
            'verified_at' => now()->subDays(fake()->numberBetween(5, 200)),
            'is_active' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => Dealership::STATUS_PENDING,
            'verified_at' => null,
        ]);
    }
}