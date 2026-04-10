<?php

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Mail\BookingReceived;
use App\Mail\VerifyBookingEmail;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();

    $this->category = RoomCategory::factory()->create([
        'price_per_night' => 3000,
        'max_capacity' => 4,
        'is_active' => true,
    ]);

    $this->room = Room::factory()->create([
        'room_category_id' => $this->category->id,
        'status' => RoomStatus::Available,
    ]);
});

test('submitting booking form sends verification email and advances to step 2', function () {
    Livewire::test('pages::booking.create', ['slug' => $this->category->slug])
        ->set('guest_name', 'Juan Dela Cruz')
        ->set('guest_email', 'juan@example.com')
        ->set('guest_phone', '09171234567')
        ->set('check_in_date', today()->addDay()->toDateString())
        ->set('check_out_date', today()->addDays(3)->toDateString())
        ->set('num_guests', 2)
        ->call('book')
        ->assertSet('step', 2)
        ->assertHasNoErrors();

    Mail::assertSent(VerifyBookingEmail::class, function ($mail) {
        return $mail->hasTo('juan@example.com')
            && $mail->guestName === 'Juan Dela Cruz';
    });
});

test('wrong verification code rejects booking', function () {
    $cacheKey = 'booking_verify_'.md5('juan@example.com');
    Cache::put($cacheKey, '123456', now()->addMinutes(10));

    Livewire::test('pages::booking.create', ['slug' => $this->category->slug])
        ->set('guest_name', 'Juan Dela Cruz')
        ->set('guest_email', 'juan@example.com')
        ->set('guest_phone', '09171234567')
        ->set('check_in_date', today()->addDay()->toDateString())
        ->set('check_out_date', today()->addDays(3)->toDateString())
        ->set('num_guests', 2)
        ->set('step', 2)
        ->set('verificationCode', '999999')
        ->call('verifyCode')
        ->assertHasErrors(['verificationCode']);

    $this->assertDatabaseCount('bookings', 0);
});

test('correct verification code creates booking and sends booking received email', function () {
    $cacheKey = 'booking_verify_'.md5('juan@example.com');
    Cache::put($cacheKey, '123456', now()->addMinutes(10));

    Livewire::test('pages::booking.create', ['slug' => $this->category->slug])
        ->set('guest_name', 'Juan Dela Cruz')
        ->set('guest_email', 'juan@example.com')
        ->set('guest_phone', '09171234567')
        ->set('check_in_date', today()->addDay()->toDateString())
        ->set('check_out_date', today()->addDays(3)->toDateString())
        ->set('num_guests', 2)
        ->set('step', 2)
        ->set('verificationCode', '123456')
        ->call('verifyCode')
        ->assertRedirect();

    $this->assertDatabaseHas('bookings', [
        'guest_email' => 'juan@example.com',
        'booking_status' => BookingStatus::Pending,
    ]);

    Mail::assertSent(BookingReceived::class, function ($mail) {
        return $mail->hasTo('juan@example.com');
    });
});

test('resend code sends new verification email', function () {
    Livewire::test('pages::booking.create', ['slug' => $this->category->slug])
        ->set('guest_name', 'Juan Dela Cruz')
        ->set('guest_email', 'juan@example.com')
        ->set('guest_phone', '09171234567')
        ->set('check_in_date', today()->addDay()->toDateString())
        ->set('check_out_date', today()->addDays(3)->toDateString())
        ->set('step', 2)
        ->call('resendCode')
        ->assertHasNoErrors();

    Mail::assertSent(VerifyBookingEmail::class);
});

test('admin confirming booking sends confirmation email', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->create([
        'booking_status' => BookingStatus::Pending,
        'guest_email' => 'guest@example.com',
        'room_id' => $this->room->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::bookings.show', ['booking' => $booking])
        ->call('confirm');

    Mail::assertSent(BookingConfirmed::class, function ($mail) {
        return $mail->hasTo('guest@example.com');
    });

    $booking->refresh();
    expect($booking->booking_status)->toBe(BookingStatus::Confirmed);
});

test('admin cancelling booking sends cancellation email', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->create([
        'booking_status' => BookingStatus::Pending,
        'guest_email' => 'guest@example.com',
        'room_id' => $this->room->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::bookings.show', ['booking' => $booking])
        ->call('cancel');

    Mail::assertSent(BookingCancelled::class, function ($mail) {
        return $mail->hasTo('guest@example.com');
    });

    $booking->refresh();
    expect($booking->booking_status)->toBe(BookingStatus::Cancelled);
});
