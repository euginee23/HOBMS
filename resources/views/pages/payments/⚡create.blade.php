<?php

use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Record Payment')] class extends Component {
    public Booking $booking;

    public string $amount = '';
    public string $payment_method = 'cash';
    public string $remarks = '';

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load('room.roomCategory');
        $this->amount = (string) $booking->balance_remaining;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $this->booking->balance_remaining],
            'payment_method' => ['required', 'in:' . implode(',', array_column(PaymentMethod::cases(), 'value'))],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'received_by' => Auth::id(),
            'remarks' => $validated['remarks'],
            'paid_at' => now(),
        ]);

        session()->flash('success', 'Payment recorded successfully.');
        $this->redirect(route('bookings.show', $this->booking), navigate: true);
    }
}; ?>

<div>
    <div class="max-w-2xl space-y-6">
        <div>
            <flux:button variant="ghost" size="sm" :href="route('bookings.show', $booking)" wire:navigate icon="arrow-left">Back to Booking</flux:button>
            <flux:heading size="xl" class="mt-2">{{ __('Record Payment') }}</flux:heading>
        </div>

        {{-- Booking Summary --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <dl class="grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Booking Reference</dt>
                    <dd class="font-medium text-zinc-900 dark:text-white">{{ $booking->booking_reference }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Guest</dt>
                    <dd class="font-medium text-zinc-900 dark:text-white">{{ $booking->guest_name }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Total Amount</dt>
                    <dd class="font-medium text-zinc-900 dark:text-white">₱{{ number_format($booking->total_amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Balance Remaining</dt>
                    <dd class="font-bold text-red-600 dark:text-red-400">₱{{ number_format($booking->balance_remaining, 2) }}</dd>
                </div>
            </dl>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="amount" label="Amount (₱)" type="number" step="0.01" min="0.01" max="{{ $booking->balance_remaining }}" required />

            <flux:select wire:model="payment_method" label="Payment Method" required>
                @foreach(PaymentMethod::cases() as $method)
                    <flux:select.option value="{{ $method->value }}">{{ $method->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="remarks" label="Remarks" placeholder="Optional notes..." rows="2" />

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">Record Payment</flux:button>
                <flux:button variant="ghost" :href="route('bookings.show', $booking)" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
