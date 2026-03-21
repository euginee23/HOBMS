<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Walk-in Booking')] class extends Component {
    public string $guest_name = '';
    public string $guest_email = '';
    public string $guest_phone = '';
    public string $room_category_id = '';
    public string $room_id = '';
    public string $check_in_date = '';
    public string $check_out_date = '';
    public int $num_guests = 1;
    public string $special_requests = '';

    public function save(): void
    {
        $validated = $this->validate([
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'num_guests' => ['required', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ]);

        $room = Room::with('roomCategory')->findOrFail($validated['room_id']);

        if (! $room->isAvailableForDates($validated['check_in_date'], $validated['check_out_date'])) {
            $this->addError('room_id', 'This room is not available for the selected dates.');

            return;
        }

        $nights = \Carbon\Carbon::parse($validated['check_in_date'])->diffInDays(\Carbon\Carbon::parse($validated['check_out_date']));

        $booking = Booking::create([
            ...$validated,
            'booking_status' => BookingStatus::Confirmed,
            'payment_status' => PaymentStatus::Unpaid,
            'price_per_night' => $room->roomCategory->price_per_night,
            'total_amount' => $room->roomCategory->price_per_night * $nights,
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        session()->flash('success', "Booking {$booking->booking_reference} created successfully.");
        $this->redirect(route('bookings.show', $booking), navigate: true);
    }

    #[Computed]
    public function categories()
    {
        return RoomCategory::active()->orderBy('name')->get();
    }

    #[Computed]
    public function availableRooms()
    {
        if (! $this->room_category_id) {
            return collect();
        }

        $query = Room::available()->where('room_category_id', $this->room_category_id);

        if ($this->check_in_date && $this->check_out_date) {
            $rooms = $query->get()->filter(fn (Room $room) => $room->isAvailableForDates($this->check_in_date, $this->check_out_date));

            return $rooms;
        }

        return $query->get();
    }
}; ?>

<div>
    <div class="max-w-2xl space-y-6">
        <div>
            <flux:button variant="ghost" size="sm" :href="route('bookings.index')" wire:navigate icon="arrow-left">Back to Bookings</flux:button>
            <flux:heading size="xl" class="mt-2">{{ __('Walk-in Booking') }}</flux:heading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">Guest Information</flux:heading>
                <div class="space-y-4">
                    <flux:input wire:model="guest_name" label="Guest Name" required />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model="guest_email" label="Email" type="email" required />
                        <flux:input wire:model="guest_phone" label="Phone" required />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">Room & Dates</flux:heading>
                <div class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model.live="check_in_date" label="Check-in Date" type="date" required />
                        <flux:input wire:model.live="check_out_date" label="Check-out Date" type="date" required />
                    </div>

                    <flux:select wire:model.live="room_category_id" label="Room Category" required>
                        <flux:select.option value="">Select Category</flux:select.option>
                        @foreach($this->categories as $cat)
                            <flux:select.option value="{{ $cat->id }}">{{ $cat->name }} (₱{{ number_format($cat->price_per_night, 2) }}/night, max {{ $cat->max_capacity }} guests)</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="room_id" label="Room" required>
                        <flux:select.option value="">Select Room</flux:select.option>
                        @foreach($this->availableRooms as $room)
                            <flux:select.option value="{{ $room->id }}">Room {{ $room->room_number }} (Floor {{ $room->floor }})</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="num_guests" label="Number of Guests" type="number" min="1" required />

                    <flux:textarea wire:model="special_requests" label="Special Requests" rows="2" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">Create Booking</flux:button>
                <flux:button variant="ghost" :href="route('bookings.index')" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
