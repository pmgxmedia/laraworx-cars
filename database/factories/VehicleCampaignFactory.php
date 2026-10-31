<?php

namespace Database\Factories;

use App\Models\VehicleCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VehicleCampaign>
 */
class VehicleCampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ulid' => (string) Str::ulid(),
            'name' => fake()->randomElement(['Buy & Get Paid', 'End of Year Reward Drive', 'Festive Buyer Reward', 'Winter Clearance Rewards', 'Fleet Focus Month']),
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'description' => fake()->paragraph(3),
            'terms' => 'Buyer rewards are subject to eligibility. A successful qualifying purchase must be verified before any reward is payable.',
            'start_date' => now()->subDays(fake()->numberBetween(1, 90)),
            'end_date' => now()->addDays(fake()->numberBetween(10, 120)),
            'status' => VehicleCampaign::STATUS_ACTIVE,
            'is_featured' => fake()->boolean(40),
            'created_by' => 1,
        ];
    }
}