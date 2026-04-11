<?php

use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\Complaint;
use Livewire\Livewire;

test('guest can submit complaint from landing complaint component', function () {
    $booking = Booking::factory()->create();

    Livewire::test('pages::portal.complaint')
        ->set('booking_reference', $booking->booking_reference)
        ->set('subject', 'Air conditioning issue')
        ->set('description', 'The room AC is not cooling properly.')
        ->call('submitComplaint')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('complaints', [
        'booking_id' => $booking->id,
        'subject' => 'Air conditioning issue',
        'complaint_status' => ComplaintStatus::Open->value,
    ]);
});

test('guest complaint requires valid booking reference', function () {
    Livewire::test('pages::portal.complaint')
        ->set('booking_reference', 'BK-INVALID-0000')
        ->set('subject', 'Issue')
        ->set('description', 'Test description')
        ->call('submitComplaint')
        ->assertHasErrors(['booking_reference']);

    expect(Complaint::count())->toBe(0);
});
