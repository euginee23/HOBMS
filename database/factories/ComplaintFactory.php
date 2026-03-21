<?php

namespace Database\Factories;

use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
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
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'complaint_status' => ComplaintStatus::Open,
        ];
    }

    /**
     * Set complaint as in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'complaint_status' => ComplaintStatus::InProgress,
        ]);
    }

    /**
     * Set complaint as resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'complaint_status' => ComplaintStatus::Resolved,
            'admin_response' => fake()->paragraph(),
            'resolved_by' => User::factory()->admin(),
            'resolved_at' => now(),
        ]);
    }

    /**
     * Set complaint as closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'complaint_status' => ComplaintStatus::Closed,
            'admin_response' => fake()->paragraph(),
            'resolved_by' => User::factory()->admin(),
            'resolved_at' => now()->subDay(),
        ]);
    }
}
