<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->randomElement([189900, 214900, 249900, 289900, 314900, 355000, 379900, 419900, 449900, 525900, 619900, 759900]);
        $year = fake()->numberBetween(2016, 2025);
        $mileage = fake()->numberBetween(15000, 120000);

        return [
            'ulid' => (string) Str::ulid(),
            'vehicle_reference' => 'WC-'.strtoupper(Str::random(6)),
            'seller_type' => 'dealership',
            'status' => Vehicle::STATUS_PUBLISHED,
            'make' => fake()->randomElement(['Toyota', 'Volkswagen', 'Ford', 'BMW', 'Mercedes-Benz', 'Audi', 'Hyundai', 'Kia', 'Nissan', 'Isuzu', 'Mahindra', 'Suzuki']),
            'model' => fake()->randomElement(['Hilux', 'Ranger', 'Golf', 'Polo', 'Corolla', 'RAV4', 'X1', 'A3', 'i20', 'Sportage', 'Navara', 'D-Max', 'Scorpio', 'Swift']),
            'variant' => fake()->randomElement(['2.8 GD-6 RB6', '1.4 TSI', '1.0 TSI', '2.0 TDI', '20D M Sport', '35 TFSI', '1.6 CRDi', '2.2D', '1.5 Turbo']),
            'year' => $year,
            'mileage' => $mileage,
            'price' => $price,
            'currency' => 'ZAR',
            'transmission' => fake()->randomElement(['Automatic', 'Manual']),
            'fuel_type' => fake()->randomElement(['Petrol', 'Diesel', 'Hybrid', 'Electric']),
            'body_type' => fake()->randomElement(['SUV', 'Hatchback', 'Sedan', 'Bakkie', 'Coupe', 'Sports', 'MPV']),
            'engine_cc' => fake()->randomElement([1000, 1200, 1400, 1600, 1800, 2000, 2200, 2800, 3000]),
            'power_kw' => fake()->randomElement([70, 85, 92, 103, 110, 118, 135, 150, 162, 180, 235]),
            'torque_nm' => fake()->randomElement([150, 175, 200, 230, 250, 320, 360, 420, 500]),
            'colour' => fake()->randomElement(['Pearl White', 'Graphite Grey', 'Phantom Black', 'Silver', 'Arctic White', 'Deep Blue', 'Crimson Red', 'Bronze']),
            'doors' => fake()->randomElement([4, 4, 5]),
            'seats' => 5,
            'condition' => 'used',
            'vehicle_condition_detail' => fake()->randomElement(['Excellent', 'Very Good', 'Good', 'Immaculate']),
            'registration_status' => fake()->randomElement(['registered', 'registered', 'unregistered']),
            'description' => fake()->paragraph(4),
            'features' => fake()->randomElements([
                'Air Conditioning', 'Cruise Control', 'Reverse Camera', 'Bluetooth', 'Leather Seats',
                'Sunroof', 'Alloy Wheels', 'Navigation', 'Adaptive Cruise', 'Parking Sensors',
                'Apple CarPlay', 'Android Auto', 'Heated Seats', 'ESP', 'Traction Control',
            ], fake()->numberBetween(5, 10)),
            'location_province' => fake()->randomElement(['Gauteng', 'Western Cape', 'KwaZulu-Natal', 'Eastern Cape']),
            'location_city' => fake()->randomElement(['Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Centurion', 'Midrand']),
            'location_suburb' => fake()->city(),
            'is_featured' => fake()->boolean(30),
            'views_count' => fake()->numberBetween(20, 900),
            'slug' => Str::slug(fake()->unique()->words(3, true).'-'.$year),
            'date_first_registered' => fake()->dateTimeBetween("{$year}-01-01", "{$year}-12-31"),
            'published_at' => now()->subDays(fake()->numberBetween(1, 120)),
            'expires_at' => now()->addDays(60),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => Vehicle::STATUS_PUBLISHED, 'published_at' => now()]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}