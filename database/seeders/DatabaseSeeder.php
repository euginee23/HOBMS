<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure room-categories storage directory exists
        Storage::disk('public')->makeDirectory('room-categories');

        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@hobms.test',
        ]);

        // Create receptionist user
        User::factory()->receptionist()->create([
            'name' => 'Front Desk',
            'email' => 'receptionist@hobms.test',
        ]);

        // Seed domain data
        $this->call([
            RoomCategorySeeder::class,
            RoomSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}
