<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_category_id' => RoomCategory::factory(),
            'room_number' => fake()->unique()->numerify('###'),
            'floor' => (string) fake()->numberBetween(1, 5),
            'status' => RoomStatus::Available,
        ];
    }

    /**
     * Set room as occupied.
     */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RoomStatus::Occupied,
        ]);
    }

    /**
     * Set room as under maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RoomStatus::Maintenance,
        ]);
    }
}
