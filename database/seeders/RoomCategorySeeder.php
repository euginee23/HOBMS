<?php

namespace Database\Seeders;

use App\Models\RoomCategory;
use Illuminate\Database\Seeder;

class RoomCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Standard',
                'description' => 'A comfortable room with essential amenities, perfect for budget-conscious travelers seeking a clean and cozy stay.',
                'price_per_night' => 1500.00,
                'max_capacity' => 2,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Private Bathroom'],
            ],
            [
                'name' => 'Deluxe',
                'description' => 'A spacious room with upgraded furnishings and additional amenities for a more refined experience.',
                'price_per_night' => 2500.00,
                'max_capacity' => 2,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Mini Bar', 'Private Bathroom', 'Room Service'],
            ],
            [
                'name' => 'Superior',
                'description' => 'A premium room offering generous space, elegant décor, and a curated selection of luxury amenities.',
                'price_per_night' => 3500.00,
                'max_capacity' => 3,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Mini Bar', 'Balcony', 'Room Service', 'Safe'],
            ],
            [
                'name' => 'Suite',
                'description' => 'A lavish suite with a separate living area, premium furnishings, and panoramic views for the ultimate luxury stay.',
                'price_per_night' => 6000.00,
                'max_capacity' => 4,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Mini Bar', 'Balcony', 'Ocean View', 'Room Service', 'Safe', 'Jacuzzi'],
            ],
            [
                'name' => 'Family',
                'description' => 'A generously sized room designed for families, featuring multiple beds and child-friendly amenities.',
                'price_per_night' => 4500.00,
                'max_capacity' => 6,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Private Bathroom', 'Room Service', 'Extra Beds'],
            ],
        ];

        foreach ($categories as $category) {
            RoomCategory::create($category);
        }
    }
}
