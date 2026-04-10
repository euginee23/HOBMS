<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
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
        $categories = RoomCategory::all();

        $roomMap = [
            'Standard' => ['101', '102', '103', '104', '105', '106', '107', '108'],
            'Deluxe' => ['201', '202', '203', '204', '205', '206'],
            'Superior' => ['301', '302', '303', '304', '305'],
            'Suite' => ['401', '402', '403'],
            'Family' => ['501', '502', '503', '504'],
        ];

        foreach ($categories as $category) {
            $rooms = $roomMap[$category->name] ?? [];

            foreach ($rooms as $roomNumber) {
                $floor = substr($roomNumber, 0, 1);

                Room::create([
                    'room_category_id' => $category->id,
                    'room_number' => $roomNumber,
                    'floor' => $floor,
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
