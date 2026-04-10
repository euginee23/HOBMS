<?php

namespace Database\Factories;

use App\Models\RoomCategory;
use App\Models\RoomCategoryImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomCategoryImage>
 */
class RoomCategoryImageFactory extends Factory
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
            'image_path' => 'room-categories/gallery/'.fake()->uuid().'.jpg',
            'sort_order' => fake()->numberBetween(0, 4),
        ];
    }
}
