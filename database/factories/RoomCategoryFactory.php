<?php

namespace Database\Factories;

use App\Models\RoomCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomCategory>
 */
class RoomCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Standard', 'Deluxe', 'Superior', 'Suite', 'Family',
            'Executive', 'Premium', 'Economy',
        ]);

        $maxCapacity = fake()->numberBetween(2, 6);

        return [
            'name' => $name,
            'description' => fake()->paragraph(),
            'price_per_night' => fake()->randomFloat(2, 1500, 8000),
            'max_capacity' => $maxCapacity,
            'room_size_sqm' => fake()->numberBetween(18, 60),
            'base_occupancy' => fake()->numberBetween(1, min(3, $maxCapacity)),
            'extra_person_charge' => fake()->randomFloat(2, 300, 1000),
            'amenities' => fake()->randomElements(
                ['Wi-Fi', 'Air Conditioning', 'TV', 'Mini Bar', 'Balcony', 'Ocean View', 'Room Service', 'Safe'],
                fake()->numberBetween(3, 6),
            ),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
