<?php

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component {
    #[Computed]
    public function todayCheckIns(): int
    {
        return Booking::query()
            ->where('check_in_date', today())
            ->whereIn('booking_status', [BookingStatus::Confirmed, BookingStatus::CheckedIn])
            ->count();
    }

    #[Computed]
    public function todayCheckOuts(): int
    {
        return Booking::query()
            ->where('check_out_date', today())
            ->where('booking_status', BookingStatus::CheckedIn)
            ->count();
    }

    #[Computed]
    public function availableRooms(): int
    {
        return Room::available()->count();
    }

    #[Computed]
    public function totalRooms(): int
    {
        return Room::query()->count();
    }

    #[Computed]
    public function monthlyEarnings(): float
    {
        return (float) Payment::query()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
    }

    #[Computed]
    public function pendingBookings(): int
    {
        return Booking::query()
            ->where('booking_status', BookingStatus::Pending)
            ->count();
    }

    #[Computed]
    public function recentBookings()
    {
        return Booking::query()
            ->with('room.roomCategory')
            ->latest()
            ->take(10)
            ->get();
    }

    #[Computed]
    public function roomStatusSummary()
    {
        return Room::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
    }
}; ?>

<div>
    <div class="space-y-6">
        <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>

        {{-- Stat Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <flux:icon.arrow-down-on-square class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Today's Check-ins</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->todayCheckIns }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                        <flux:icon.arrow-up-on-square class="size-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Today's Check-outs</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->todayCheckOuts }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-green-50 dark:bg-green-900/20">
                        <flux:icon.building-office class="size-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Rooms Available</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->availableRooms }} / {{ $this->totalRooms }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/20">
                        <flux:icon.banknotes class="size-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Monthly Earnings</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">₱{{ number_format($this->monthlyEarnings, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Recent Bookings --}}
            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 lg:col-span-2">
                <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    <flux:heading size="lg">Recent Bookings</flux:heading>
                    <flux:button variant="ghost" size="sm" :href="route('bookings.index')" wire:navigate>View All</flux:button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                            <tr>
                                <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Reference</th>
                                <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Guest</th>
                                <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Room</th>
                                <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                                <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Check-in</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($this->recentBookings as $booking)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="px-6 py-3">
                                        <a href="{{ route('bookings.show', $booking) }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                                            {{ $booking->booking_reference }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3 text-zinc-900 dark:text-zinc-100">{{ $booking->guest_name }}</td>
                                    <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">
                                        {{ $booking->room?->room_number }} - {{ $booking->room?->roomCategory?->name }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <flux:badge size="sm" :color="$booking->booking_status->color()">{{ $booking->booking_status->label() }}</flux:badge>
                                    </td>
                                    <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">{{ $booking->check_in_date->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">No bookings yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Room Status Overview --}}
            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    <flux:heading size="lg">Room Status</flux:heading>
                </div>
                <div class="space-y-4 p-6">
                    @foreach(\App\Enums\RoomStatus::cases() as $status)
                        @php $count = $this->roomStatusSummary[$status->value] ?? 0; @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <flux:badge size="sm" :color="$status->color()">{{ $status->label() }}</flux:badge>
                            </div>
                            <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $count }}</span>
                        </div>
                    @endforeach

                    <div class="border-t border-zinc-200 pt-4 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pending Bookings</span>
                            <span class="text-lg font-semibold text-amber-600 dark:text-amber-400">{{ $this->pendingBookings }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
