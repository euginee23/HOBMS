<?php

use App\Models\Booking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Track Your Booking')] #[Layout('layouts.public')] class extends Component {
    public string $booking_reference = '';

    public function lookup(): void
    {
        $this->validate([
            'booking_reference' => ['required', 'string'],
        ]);

        $booking = Booking::where('booking_reference', $this->booking_reference)->first();

        if (! $booking) {
            $this->addError('booking_reference', 'No booking found with this reference number.');

            return;
        }

        $this->redirect(route('portal.view', $booking->portal_token), navigate: true);
    }
}; ?>

<div>
    <section class="flex min-h-[70vh] items-center justify-center py-16">
        <div class="mx-auto w-full max-w-md px-6">
            <div class="text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20">
                    <svg class="size-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <h1 class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">Track Your Booking</h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-400">Enter your booking reference number to view your booking details</p>
            </div>

            <form wire:submit="lookup" class="mt-8 space-y-4">
                <flux:input wire:model="booking_reference" label="Booking Reference" placeholder="BK-20250101-0001" required />
                <flux:button type="submit" variant="primary" class="w-full">Look Up Booking</flux:button>
            </form>
        </div>
    </section>
</div>
