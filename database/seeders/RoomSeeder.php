<?php

namespace Database\Seeders;

use App\Enums\BedType;
use App\Enums\RoomStatus;
use App\Enums\ViewType;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = RoomCategory::all()->keyBy('name');

        $roomMap = [
            'Standard' => [
                ['room_number' => '101', 'bed_type' => BedType::Double, 'bed_count' => 1, 'view_type' => ViewType::None],
                ['room_number' => '102', 'bed_type' => BedType::Twin, 'bed_count' => 2, 'view_type' => ViewType::None],
                ['room_number' => '103', 'bed_type' => BedType::Double, 'bed_count' => 1, 'view_type' => ViewType::Garden],
                ['room_number' => '104', 'bed_type' => BedType::Twin, 'bed_count' => 2, 'view_type' => ViewType::None],
                ['room_number' => '105', 'bed_type' => BedType::Double, 'bed_count' => 1, 'view_type' => ViewType::City],
                ['room_number' => '106', 'bed_type' => BedType::Double, 'bed_count' => 1, 'view_type' => ViewType::Garden],
                ['room_number' => '107', 'bed_type' => BedType::Single, 'bed_count' => 2, 'view_type' => ViewType::None],
                ['room_number' => '108', 'bed_type' => BedType::Double, 'bed_count' => 1, 'view_type' => ViewType::City],
            ],
            'Deluxe' => [
                ['room_number' => '201', 'bed_type' => BedType::Queen, 'bed_count' => 1, 'view_type' => ViewType::City],
                ['room_number' => '202', 'bed_type' => BedType::Queen, 'bed_count' => 1, 'view_type' => ViewType::Garden],
                ['room_number' => '203', 'bed_type' => BedType::Queen, 'bed_count' => 1, 'view_type' => ViewType::Pool],
                ['room_number' => '204', 'bed_type' => BedType::Twin, 'bed_count' => 2, 'view_type' => ViewType::City],
                ['room_number' => '205', 'bed_type' => BedType::Queen, 'bed_count' => 1, 'view_type' => ViewType::Pool],
                ['room_number' => '206', 'bed_type' => BedType::Queen, 'bed_count' => 1, 'view_type' => ViewType::Garden],
            ],
            'Superior' => [
                ['room_number' => '301', 'bed_type' => BedType::King, 'bed_count' => 1, 'view_type' => ViewType::Ocean],
                ['room_number' => '302', 'bed_type' => BedType::King, 'bed_count' => 1, 'view_type' => ViewType::Ocean],
                ['room_number' => '303', 'bed_type' => BedType::King, 'bed_count' => 1, 'view_type' => ViewType::Mountain],
                ['room_number' => '304', 'bed_type' => BedType::Queen, 'bed_count' => 1, 'view_type' => ViewType::Pool],
                ['room_number' => '305', 'bed_type' => BedType::King, 'bed_count' => 1, 'view_type' => ViewType::Ocean],
            ],
            'Suite' => [
                ['room_number' => '401', 'bed_type' => BedType::King, 'bed_count' => 1, 'view_type' => ViewType::Ocean],
                ['room_number' => '402', 'bed_type' => BedType::King, 'bed_count' => 1, 'view_type' => ViewType::Ocean],
                ['room_number' => '403', 'bed_type' => BedType::King, 'bed_count' => 1, 'view_type' => ViewType::Mountain],
            ],
            'Family' => [
                ['room_number' => '501', 'bed_type' => BedType::Queen, 'bed_count' => 2, 'view_type' => ViewType::Garden],
                ['room_number' => '502', 'bed_type' => BedType::Queen, 'bed_count' => 2, 'view_type' => ViewType::Pool],
                ['room_number' => '503', 'bed_type' => BedType::Double, 'bed_count' => 3, 'view_type' => ViewType::Garden],
                ['room_number' => '504', 'bed_type' => BedType::Queen, 'bed_count' => 2, 'view_type' => ViewType::City],
            ],
        ];

        foreach ($roomMap as $categoryName => $rooms) {
            $category = $categories->get($categoryName);

            if (! $category) {
                continue;
            }

            foreach ($rooms as $roomData) {
                Room::create([
                    'room_category_id' => $category->id,
                    'room_number' => $roomData['room_number'],
                    'floor' => substr($roomData['room_number'], 0, 1),
                    'bed_type' => $roomData['bed_type'],
                    'bed_count' => $roomData['bed_count'],
                    'view_type' => $roomData['view_type'],
                    'is_smoking' => false,
                    'status' => RoomStatus::Available,
                ]);
            }
        }

        // Set a couple of rooms to maintenance for realism
        Room::where('room_number', '104')->update(['status' => RoomStatus::Maintenance, 'notes' => 'Plumbing repair in progress']);
        Room::where('room_number', '302')->update(['status' => RoomStatus::Maintenance, 'notes' => 'Scheduled deep cleaning']);
        Room::where('room_number', '206')->update(['status' => RoomStatus::OutOfOrder, 'notes' => 'AC unit replacement']);
    }
}
