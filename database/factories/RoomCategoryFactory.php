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

        return [
            'name' => $name,
            'description' => fake()->paragraph(),
            'price_per_night' => fake()->randomFloat(2, 1500, 8000),
            'max_capacity' => fake()->numberBetween(1, 6),
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
