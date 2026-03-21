<?php

use App\Models\Payment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Payments')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function payments()
    {
        return Payment::query()
            ->with(['booking', 'receivedBy'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('receipt_number', 'like', "%{$this->search}%")
                    ->orWhereHas('booking', fn ($bq) => $bq->where('booking_reference', 'like', "%{$this->search}%")
                        ->orWhere('guest_name', 'like', "%{$this->search}%"));
            }))
            ->latest('paid_at')
            ->paginate(15);
    }
}; ?>

<div>
    <div class="space-y-6">
        <flux:heading size="xl">{{ __('Payments') }}</flux:heading>

        <flux:input wire:model.live.debounce="search" placeholder="Search receipt #, booking reference, guest name..." icon="magnifying-glass" />

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Receipt #</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Booking</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Guest</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Amount</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Method</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Received By</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->payments as $payment)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $payment->receipt_number }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('bookings.show', $payment->booking) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                                    {{ $payment->booking->booking_reference }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $payment->booking->guest_name }}</td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">₱{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $payment->payment_method->label() }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $payment->receivedBy?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $payment->paid_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $this->payments->links() }}</div>
    </div>
</div>
