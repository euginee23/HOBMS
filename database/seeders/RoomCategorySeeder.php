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
                'description' => 'A clean and comfortable room with essential amenities, perfect for solo travelers or couples looking for an affordable yet pleasant stay. Enjoy air-conditioned comfort with a private bathroom featuring hot and cold shower.',
                'price_per_night' => 1800.00,
                'max_capacity' => 2,
                'room_size_sqm' => 22,
                'base_occupancy' => 2,
                'extra_person_charge' => 500.00,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Cable TV', 'Private Bathroom', 'Hot & Cold Shower', 'Complimentary Toiletries', 'Daily Housekeeping'],
                'image_path' => 'room-categories/standard.jpg',
            ],
            [
                'name' => 'Deluxe',
                'description' => 'A spacious room with upgraded furnishings and a curated selection of amenities for a more refined experience. Features a mini-bar, work desk, and complimentary breakfast — ideal for business and leisure travelers alike.',
                'price_per_night' => 3200.00,
                'max_capacity' => 3,
                'room_size_sqm' => 30,
                'base_occupancy' => 2,
                'extra_person_charge' => 800.00,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Cable TV', 'Mini Bar', 'Private Bathroom', 'Hot & Cold Shower', 'Complimentary Breakfast', 'Room Service', 'Work Desk', 'Safety Deposit Box'],
                'image_path' => 'room-categories/deluxe.jpg',
            ],
            [
                'name' => 'Superior',
                'description' => 'A premium room offering generous space, elegant décor, and a private balcony with scenic views. Enjoy premium bedding, a seating area, and access to exclusive floor amenities for the discerning guest.',
                'price_per_night' => 4500.00,
                'max_capacity' => 3,
                'room_size_sqm' => 38,
                'base_occupancy' => 2,
                'extra_person_charge' => 800.00,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Smart TV', 'Mini Bar', 'Private Balcony', 'Hot & Cold Shower', 'Complimentary Breakfast', 'Room Service', 'Bathrobe & Slippers', 'Safety Deposit Box', 'Coffee & Tea Maker'],
                'image_path' => 'room-categories/superior.jpg',
            ],
            [
                'name' => 'Suite',
                'description' => 'A lavish suite with a separate living area, premium furnishings, and breathtaking panoramic views. Includes a jacuzzi bath, lounge area, and VIP concierge service — the ultimate luxury experience for special occasions.',
                'price_per_night' => 7500.00,
                'max_capacity' => 4,
                'room_size_sqm' => 55,
                'base_occupancy' => 2,
                'extra_person_charge' => 1000.00,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Smart TV', 'Mini Bar', 'Private Balcony', 'Ocean View', 'Hot & Cold Shower', 'Jacuzzi', 'Complimentary Breakfast', 'Room Service', 'Bathrobe & Slippers', 'Safety Deposit Box', 'Living Area', 'VIP Concierge'],
                'image_path' => 'room-categories/suite.jpg',
            ],
            [
                'name' => 'Family',
                'description' => 'A generously sized room designed for families, featuring multiple beds and child-friendly amenities. Spacious enough for the whole family with extra beds, a mini refrigerator, and complimentary breakfast for all guests.',
                'price_per_night' => 5500.00,
                'max_capacity' => 6,
                'room_size_sqm' => 48,
                'base_occupancy' => 4,
                'extra_person_charge' => 600.00,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Cable TV', 'Mini Refrigerator', 'Private Bathroom', 'Hot & Cold Shower', 'Complimentary Breakfast', 'Room Service', 'Extra Beds', 'Baby Crib Available', 'Daily Housekeeping'],
                'image_path' => 'room-categories/family.jpg',
            ],
        ];

        foreach ($categories as $category) {
            RoomCategory::create($category);
        }
    }
}
