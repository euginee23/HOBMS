<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Booking Details')] class extends Component {
    public Booking $booking;

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load(['room.roomCategory', 'payments.receivedBy', 'complaints', 'confirmedBy', 'checkedInBy', 'checkedOutBy']);
    }

    public function confirm(): void
    {
        $this->booking->update([
            'booking_status' => BookingStatus::Confirmed,
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        $this->booking->refresh();
        $this->booking->load('room.roomCategory');

        Mail::to($this->booking->guest_email)->send(new BookingConfirmed($this->booking));

        session()->flash('success', 'Booking confirmed. Confirmation email sent to guest.');
    }

    public function checkIn(): void
    {
        $this->booking->update([
            'booking_status' => BookingStatus::CheckedIn,
            'checked_in_by' => Auth::id(),
            'checked_in_at' => now(),
        ]);

        $this->booking->room->update(['status' => \App\Enums\RoomStatus::Occupied]);
        $this->booking->refresh();
        session()->flash('success', 'Guest checked in.');
    }

    public function checkOut(): void
    {
        $this->booking->update([
            'booking_status' => BookingStatus::CheckedOut,
            'checked_out_by' => Auth::id(),
            'checked_out_at' => now(),
        ]);

        $this->booking->room->update(['status' => \App\Enums\RoomStatus::Available]);
        $this->booking->refresh();
        session()->flash('success', 'Guest checked out.');
    }

    public function cancel(): void
    {
        $this->booking->update([
            'booking_status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $this->booking->refresh();
        $this->booking->load('room.roomCategory');

        Mail::to($this->booking->guest_email)->send(new BookingCancelled($this->booking));

        session()->flash('success', 'Booking cancelled. Cancellation email sent to guest.');
    }

    public function markNoShow(): void
    {
        $this->booking->update([
            'booking_status' => BookingStatus::NoShow,
        ]);

        $this->booking->refresh();
        session()->flash('success', 'Booking marked as no-show.');
    }
}; ?>

<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:button variant="ghost" size="sm" :href="route('bookings.index')" wire:navigate icon="arrow-left">Back to Bookings</flux:button>
                <flux:heading size="xl" class="mt-2">{{ $booking->booking_reference }}</flux:heading>
            </div>
            <div class="flex items-center gap-2">
                @if($booking->booking_status === BookingStatus::Pending)
                    <flux:button variant="primary" wire:click="confirm" wire:confirm="Confirm this booking?">Confirm</flux:button>
                    <flux:button variant="danger" wire:click="cancel" wire:confirm="Cancel this booking?">Cancel</flux:button>
                @endif
                @if($booking->booking_status === BookingStatus::Confirmed)
                    <flux:button variant="primary" wire:click="checkIn" wire:confirm="Check in this guest?">Check In</flux:button>
                    <flux:button wire:click="markNoShow" wire:confirm="Mark as no-show?">No Show</flux:button>
                    <flux:button variant="danger" wire:click="cancel" wire:confirm="Cancel this booking?">Cancel</flux:button>
                @endif
                @if($booking->booking_status === BookingStatus::CheckedIn)
                    <flux:button variant="primary" wire:click="checkOut" wire:confirm="Check out this guest?">Check Out</flux:button>
                @endif
                @if(! in_array($booking->payment_status, [PaymentStatus::Paid]) && ! in_array($booking->booking_status, [BookingStatus::Cancelled, BookingStatus::NoShow]))
                    <flux:button :href="route('payments.create', $booking)" wire:navigate>Record Payment</flux:button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Booking Info --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">Booking Information</flux:heading>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Status</dt>
                        <dd><flux:badge :color="$booking->booking_status->color()">{{ $booking->booking_status->label() }}</flux:badge></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Check-in</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->check_in_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Check-out</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->check_out_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Nights</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->nights }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Room</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->room?->room_number }} - {{ $booking->room?->roomCategory?->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Guests</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->num_guests }}</dd>
                    </div>
                    @if($booking->special_requests)
                        <div>
                            <dt class="text-zinc-500 dark:text-zinc-400">Special Requests</dt>
                            <dd class="mt-1 text-zinc-900 dark:text-white">{{ $booking->special_requests }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Guest Info --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">Guest Information</flux:heading>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Name</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->guest_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Email</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->guest_email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Phone</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->guest_phone }}</dd>
                    </div>
                </dl>

                <flux:separator class="my-4" />

                <flux:heading size="lg" class="mb-4">Payment Summary</flux:heading>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Payment Status</dt>
                        <dd><flux:badge :color="$booking->payment_status->color()">{{ $booking->payment_status->label() }}</flux:badge></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Price/Night</dt>
                        <dd class="text-zinc-900 dark:text-white">₱{{ number_format($booking->price_per_night, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Total Amount</dt>
                        <dd class="text-lg font-bold text-zinc-900 dark:text-white">₱{{ number_format($booking->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Amount Paid</dt>
                        <dd class="text-zinc-900 dark:text-white">₱{{ number_format($booking->amount_paid, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Balance</dt>
                        <dd class="font-semibold {{ $booking->balance_remaining > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            ₱{{ number_format($booking->balance_remaining, 2) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Payments Table --}}
        <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
                <flux:heading size="lg">Payment History</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Receipt #</th>
                            <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Amount</th>
                            <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Method</th>
                            <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Received By</th>
                            <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($booking->payments as $payment)
                            <tr>
                                <td class="px-6 py-3 font-medium text-zinc-900 dark:text-white">{{ $payment->receipt_number }}</td>
                                <td class="px-6 py-3 text-zinc-900 dark:text-zinc-100">₱{{ number_format($payment->amount, 2) }}</td>
                                <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">{{ $payment->payment_method->label() }}</td>
                                <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">{{ $payment->receivedBy?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">{{ $payment->paid_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-zinc-500 dark:text-zinc-400">No payments recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
