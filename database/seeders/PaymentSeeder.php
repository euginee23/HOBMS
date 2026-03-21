<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $receptionist = User::where('role', 'receptionist')->first();
        $admin = User::where('role', 'admin')->first();

        $bookings = Booking::whereIn('booking_status', [
            BookingStatus::CheckedOut->value,
            BookingStatus::CheckedIn->value,
            BookingStatus::Confirmed->value,
        ])->get();

        foreach ($bookings as $booking) {
            if ($booking->booking_status === BookingStatus::CheckedOut) {
                Payment::withoutEvents(function () use ($booking, $receptionist): void {
                    Payment::create([
                        'receipt_number' => Payment::generateReceiptNumber(),
                        'booking_id' => $booking->id,
                        'amount' => $booking->total_amount,
                        'payment_method' => fake()->randomElement(PaymentMethod::cases()),
                        'received_by' => $receptionist->id,
                        'paid_at' => $booking->checked_out_at ?? $booking->check_out_date,
                    ]);
                });
            } elseif ($booking->booking_status === BookingStatus::CheckedIn && fake()->boolean(60)) {
                $amount = round((float) $booking->total_amount * fake()->randomFloat(2, 0.3, 0.7), 2);

                Payment::withoutEvents(function () use ($booking, $amount, $admin): void {
                    Payment::create([
                        'receipt_number' => Payment::generateReceiptNumber(),
                        'booking_id' => $booking->id,
                        'amount' => $amount,
                        'payment_method' => fake()->randomElement(PaymentMethod::cases()),
                        'received_by' => $admin->id,
                        'paid_at' => $booking->checked_in_at ?? $booking->check_in_date,
                    ]);
                });

                $booking->update([
                    'amount_paid' => $amount,
                    'payment_status' => 'partially_paid',
                ]);
            }
        }
    }
}
