<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Bookings')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $paymentFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPaymentFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function bookings()
    {
        return Booking::query()
            ->with('room.roomCategory')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('booking_reference', 'like', "%{$this->search}%")
                    ->orWhere('guest_name', 'like', "%{$this->search}%")
                    ->orWhere('guest_email', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('booking_status', $this->statusFilter))
            ->when($this->paymentFilter, fn ($q) => $q->where('payment_status', $this->paymentFilter))
            ->latest()
            ->paginate(15);
    }
}; ?>

<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Bookings') }}</flux:heading>
            <flux:button variant="primary" :href="route('bookings.create')" wire:navigate>
                {{ __('Walk-in Booking') }}
            </flux:button>
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce="search" placeholder="Search reference, guest name, email..." icon="magnifying-glass" />
            </div>
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach(BookingStatus::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="paymentFilter" placeholder="All Payments">
                <flux:select.option value="">All Payment Statuses</flux:select.option>
                @foreach(PaymentStatus::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Reference</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Guest</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Room</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Check-in</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Check-out</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Payment</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->bookings as $booking)
                        <tr class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50" wire:click="$dispatch('navigate', { url: '{{ route('bookings.show', $booking) }}' })" onclick="Livewire.navigate('{{ route('bookings.show', $booking) }}')">
                            <td class="px-6 py-4 font-medium text-blue-600 dark:text-blue-400">{{ $booking->booking_reference }}</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">{{ $booking->guest_name }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $booking->room?->room_number }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $booking->check_in_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $booking->check_out_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" :color="$booking->booking_status->color()">{{ $booking->booking_status->label() }}</flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" :color="$booking->payment_status->color()">{{ $booking->payment_status->label() }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">₱{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $this->bookings->links() }}</div>
    </div>
</div>
