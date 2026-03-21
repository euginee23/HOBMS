<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => fake()->randomFloat(2, 500, 5000),
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'received_by' => User::factory()->receptionist(),
            'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
