<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-15 days', '+15 days');
        $checkOut = (clone $checkIn)->modify('+'.fake()->numberBetween(1, 5).' days');
        $pricePerNight = fake()->randomFloat(2, 1500, 8000);
        $nights = (int) $checkIn->diff($checkOut)->days;

        return [
            'room_id' => Room::factory(),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'num_guests' => fake()->numberBetween(1, 4),
            'booking_status' => BookingStatus::Pending,
            'payment_status' => PaymentStatus::Unpaid,
            'price_per_night' => $pricePerNight,
            'total_amount' => $pricePerNight * $nights,
            'amount_paid' => 0,
        ];
    }

    /**
     * Set booking as confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatus::Confirmed,
            'confirmed_by' => User::factory()->admin(),
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Set booking as checked in.
     */
    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatus::CheckedIn,
            'confirmed_by' => User::factory()->admin(),
            'confirmed_at' => now()->subHours(2),
            'checked_in_by' => User::factory()->receptionist(),
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Set booking as checked out.
     */
    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatus::CheckedOut,
            'confirmed_by' => User::factory()->admin(),
            'confirmed_at' => now()->subDays(3),
            'checked_in_by' => User::factory()->receptionist(),
            'checked_in_at' => now()->subDays(2),
            'checked_out_by' => User::factory()->receptionist(),
            'checked_out_at' => now(),
            'payment_status' => PaymentStatus::Paid,
        ]);
    }

    /**
     * Set booking as cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Set booking as no-show.
     */
    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatus::NoShow,
        ]);
    }

    /**
     * Set booking as fully paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatus::Paid,
            'amount_paid' => $attributes['total_amount'] ?? 0,
        ]);
    }
}
