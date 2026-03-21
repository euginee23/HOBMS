<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Room;
use App\Models\RoomCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Book a Room')] #[Layout('layouts.public')] class extends Component {
    public RoomCategory $category;

    public string $guest_name = '';
    public string $guest_email = '';
    public string $guest_phone = '';
    public string $check_in_date = '';
    public string $check_out_date = '';
    public int $num_guests = 1;
    public string $special_requests = '';

    public function mount(string $slug): void
    {
        $this->category = RoomCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function with(): array
    {
        $nights = 0;
        $total = 0;

        if ($this->check_in_date && $this->check_out_date && $this->check_out_date > $this->check_in_date) {
            $nights = \Carbon\Carbon::parse($this->check_in_date)->diffInDays(\Carbon\Carbon::parse($this->check_out_date));
            $total = $nights * $this->category->price_per_night;
        }

        return compact('nights', 'total');
    }

    public function book(): void
    {
        $this->validate([
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'num_guests' => ['required', 'integer', 'min:1', 'max:' . $this->category->max_capacity],
        ]);

        // Find an available room in this category
        $room = $this->category->rooms()
            ->available()
            ->get()
            ->first(fn (Room $room) => $room->isAvailableForDates($this->check_in_date, $this->check_out_date));

        if (! $room) {
            $this->addError('check_in_date', 'No rooms available for the selected dates. Please try different dates.');

            return;
        }

        $nights = \Carbon\Carbon::parse($this->check_in_date)->diffInDays(\Carbon\Carbon::parse($this->check_out_date));

        $booking = \App\Models\Booking::create([
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'guest_phone' => $this->guest_phone,
            'room_id' => $room->id,
            'check_in_date' => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'num_guests' => $this->num_guests,
            'special_requests' => $this->special_requests ?: null,
            'booking_status' => BookingStatus::Pending,
            'payment_status' => PaymentStatus::Unpaid,
            'price_per_night' => $this->category->price_per_night,
            'total_amount' => $nights * $this->category->price_per_night,
            'amount_paid' => 0,
        ]);

        $this->redirect(route('booking.confirmation', $booking->portal_token), navigate: true);
    }
}; ?>

<div>
    <section class="py-16">
        <div class="mx-auto max-w-2xl px-6">
            <div class="mb-8">
                <a href="{{ route('rooms.show', $category->slug) }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400" wire:navigate>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    Back to {{ $category->name }}
                </a>
            </div>

            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Book {{ $category->name }}</h1>
            <p class="mt-2 text-zinc-600 dark:text-zinc-400">₱{{ number_format($category->price_per_night) }} per night &middot; Up to {{ $category->max_capacity }} guests</p>

            <form wire:submit="book" class="mt-8 space-y-6">
                {{-- Guest Information --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 space-y-4">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Guest Information</h2>
                    <flux:input wire:model="guest_name" label="Full Name" placeholder="Juan Dela Cruz" required />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model="guest_email" label="Email" type="email" placeholder="guest@example.com" required />
                        <flux:input wire:model="guest_phone" label="Phone" placeholder="09XX-XXX-XXXX" required />
                    </div>
                </div>

                {{-- Stay Details --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 space-y-4">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Stay Details</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model.live="check_in_date" label="Check-in Date" type="date" :min="date('Y-m-d')" required />
                        <flux:input wire:model.live="check_out_date" label="Check-out Date" type="date" :min="$check_in_date ?: date('Y-m-d')" required />
                    </div>
                    <flux:input wire:model="num_guests" label="Number of Guests" type="number" min="1" :max="$category->max_capacity" required />
                    <flux:textarea wire:model="special_requests" label="Special Requests (optional)" placeholder="Any special requirements..." rows="3" />
                </div>

                {{-- Summary --}}
                @if($nights > 0)
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-6 dark:border-blue-800 dark:bg-blue-900/20">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Booking Summary</h2>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-zinc-600 dark:text-zinc-400">Room Type</dt>
                                <dd class="font-medium text-zinc-900 dark:text-white">{{ $category->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-600 dark:text-zinc-400">Duration</dt>
                                <dd class="font-medium text-zinc-900 dark:text-white">{{ $nights }} {{ Str::plural('night', $nights) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-600 dark:text-zinc-400">Rate</dt>
                                <dd class="font-medium text-zinc-900 dark:text-white">₱{{ number_format($category->price_per_night) }} /night</dd>
                            </div>
                            <div class="flex justify-between border-t border-blue-200 pt-2 dark:border-blue-700">
                                <dt class="font-semibold text-zinc-900 dark:text-white">Total</dt>
                                <dd class="text-xl font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($total) }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <flux:button type="submit" variant="primary" class="w-full" size="lg">Submit Booking</flux:button>
            </form>
        </div>
    </section>
</div>
