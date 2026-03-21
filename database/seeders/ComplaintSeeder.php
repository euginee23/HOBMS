<?php

namespace Database\Seeders;

use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $bookings = Booking::whereIn('booking_status', ['checked_in', 'checked_out'])
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $subjects = [
            'Noisy neighbors in adjacent room',
            'Air conditioning not working properly',
            'Room was not cleaned upon arrival',
            'Hot water not available in bathroom',
            'Slow Wi-Fi connection',
            'Missing towels and toiletries',
            'Room service order took too long',
            'Broken lock on bathroom door',
        ];

        foreach ($bookings as $index => $booking) {
            $status = fake()->randomElement([
                ComplaintStatus::Open,
                ComplaintStatus::InProgress,
                ComplaintStatus::Resolved,
                ComplaintStatus::Closed,
            ]);

            $data = [
                'booking_id' => $booking->id,
                'subject' => $subjects[$index] ?? fake()->sentence(4),
                'description' => fake()->paragraph(3),
                'complaint_status' => $status,
            ];

            if (in_array($status, [ComplaintStatus::Resolved, ComplaintStatus::Closed])) {
                $data['admin_response'] = fake()->paragraph(2);
                $data['resolved_by'] = $admin->id;
                $data['resolved_at'] = now()->subDays(fake()->numberBetween(0, 5));
            }

            Complaint::create($data);
        }
    }
}
