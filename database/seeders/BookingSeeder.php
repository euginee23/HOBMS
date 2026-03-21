<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::where('status', 'available')->get();
        $admin = User::where('role', 'admin')->first();
        $receptionist = User::where('role', 'receptionist')->first();

        $guestNames = [
            'Juan Dela Cruz', 'Maria Santos', 'Jose Rizal Jr.', 'Ana Reyes',
            'Pedro Garcia', 'Sofia Mendoza', 'Carlos Tan', 'Isabella Cruz',
            'Miguel Torres', 'Camille Lim', 'Antonio Ramos', 'Grace Villar',
            'Roberto Aquino', 'Kristine Bautista', 'Fernando Diaz', 'Angela Morales',
            'Patrick Gonzales', 'Diana Fernandez', 'Mark Navarro', 'Rachel Lopez',
            'James Santiago', 'Lea Castillo', 'Vincent Aguilar', 'Nicole Perez',
            'Daniel Romero', 'Sarah Flores', 'Christian Rivera', 'Mae Simmons',
            'Andrew Pascual', 'Joyce Mercado', 'Kenneth Salazar', 'Michelle Ong',
        ];

        foreach ($guestNames as $index => $name) {
            $room = $rooms[$index % $rooms->count()];
            $daysOffset = fake()->numberBetween(-30, 15);
            $checkIn = now()->addDays($daysOffset)->startOfDay();
            $nights = fake()->numberBetween(1, 5);
            $checkOut = $checkIn->copy()->addDays($nights);
            $pricePerNight = $room->roomCategory->price_per_night;
            $totalAmount = $pricePerNight * $nights;

            $statusPool = $this->determineStatus($checkIn, $checkOut);

            $bookingData = [
                'room_id' => $room->id,
                'guest_name' => $name,
                'guest_email' => fake()->safeEmail(),
                'guest_phone' => '09'.fake()->numerify('#########'),
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'num_guests' => fake()->numberBetween(1, $room->roomCategory->max_capacity),
                'special_requests' => fake()->optional(0.3)->sentence(),
                'booking_status' => $statusPool,
                'price_per_night' => $pricePerNight,
                'total_amount' => $totalAmount,
                'amount_paid' => 0,
                'payment_status' => PaymentStatus::Unpaid,
            ];

            // Add staff tracking based on status
            if (in_array($statusPool, [BookingStatus::Confirmed, BookingStatus::CheckedIn, BookingStatus::CheckedOut])) {
                $bookingData['confirmed_by'] = $admin->id;
                $bookingData['confirmed_at'] = $checkIn->copy()->subDays(fake()->numberBetween(1, 3));
            }

            if (in_array($statusPool, [BookingStatus::CheckedIn, BookingStatus::CheckedOut])) {
                $bookingData['checked_in_by'] = $receptionist->id;
                $bookingData['checked_in_at'] = $checkIn->copy()->addHours(fake()->numberBetween(12, 15));
            }

            if ($statusPool === BookingStatus::CheckedOut) {
                $bookingData['checked_out_by'] = $receptionist->id;
                $bookingData['checked_out_at'] = $checkOut->copy()->addHours(fake()->numberBetween(8, 11));
                $bookingData['amount_paid'] = $totalAmount;
                $bookingData['payment_status'] = PaymentStatus::Paid;
            }

            if ($statusPool === BookingStatus::Cancelled) {
                $bookingData['cancelled_at'] = $checkIn->copy()->subDay();
                $bookingData['cancellation_reason'] = fake()->randomElement([
                    'Change of plans',
                    'Found a better rate elsewhere',
                    'Emergency situation',
                    'Travel restrictions',
                ]);
            }

            Booking::create($bookingData);
        }
    }

    /**
     * Determine a realistic booking status based on check-in/check-out dates.
     */
    private function determineStatus(CarbonInterface $checkIn, CarbonInterface $checkOut): BookingStatus
    {
        $today = now()->startOfDay();

        if ($checkOut->lte($today)) {
            return fake()->randomElement([
                BookingStatus::CheckedOut,
                BookingStatus::CheckedOut,
                BookingStatus::CheckedOut,
                BookingStatus::Cancelled,
                BookingStatus::NoShow,
            ]);
        }

        if ($checkIn->lte($today) && $checkOut->gt($today)) {
            return BookingStatus::CheckedIn;
        }

        return fake()->randomElement([
            BookingStatus::Pending,
            BookingStatus::Confirmed,
            BookingStatus::Confirmed,
        ]);
    }
}
