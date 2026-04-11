<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Livewire\Livewire;

test('admin can download report xlsx', function () {
    $admin = User::factory()->admin()->create();
    $category = RoomCategory::factory()->create();
    $room = Room::factory()->create(['room_category_id' => $category->id]);

    $booking = Booking::factory()->create([
        'room_id' => $room->id,
        'booking_status' => BookingStatus::Confirmed,
        'payment_status' => PaymentStatus::PartiallyPaid,
        'total_amount' => 5000,
        'amount_paid' => 2000,
    ]);

    Payment::factory()->create([
        'booking_id' => $booking->id,
        'amount' => 2000,
        'payment_method' => PaymentMethod::Cash,
        'paid_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test('pages::reports.index')
        ->set('period', 'this_month')
        ->call('downloadReport')
        ->assertFileDownloaded();
});

test('non-admin cannot access reports page', function () {
    $receptionist = User::factory()->receptionist()->create();

    $this->actingAs($receptionist)
        ->get(route('reports.index'))
        ->assertForbidden();
});
