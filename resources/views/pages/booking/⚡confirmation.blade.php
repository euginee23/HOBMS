<?php

use App\Models\Booking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Booking Confirmed')] #[Layout('layouts.public')] class extends Component {
    public Booking $booking;

    public function mount(string $token): void
    {
        $this->booking = Booking::where('portal_token', $token)
            ->with('room.roomCategory')
            ->firstOrFail();
    }
}; ?>

<div>
    <section class="py-16">
        <div class="mx-auto max-w-2xl px-6">
            <div class="text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                    <svg class="size-8 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                </div>
                <h1 class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">Booking Submitted!</h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-400">Your booking has been received and is pending confirmation.</p>
            </div>

            <div class="mt-8 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Reference Number</dt>
                        <dd class="font-mono font-semibold text-zinc-900 dark:text-white">{{ $booking->booking_reference }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Status</dt>
                        <dd><flux:badge :color="$booking->booking_status->color()">{{ $booking->booking_status->label() }}</flux:badge></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Guest</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->guest_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Room Type</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->room->roomCategory->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Check-in</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->check_in_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Check-out</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $booking->check_out_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-zinc-200 pt-2 dark:border-zinc-700">
                        <dt class="font-semibold text-zinc-900 dark:text-white">Total Amount</dt>
                        <dd class="text-lg font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($booking->total_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                <p class="text-sm text-amber-800 dark:text-amber-400">
                    <strong>Important:</strong> Save your reference number <strong>{{ $booking->booking_reference }}</strong>. You'll need it to track your booking status and submit any complaints.
                </p>
            </div>

            <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                <a href="{{ $booking->portal_url }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500" wire:navigate>
                    View Booking Details
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 px-6 py-3 text-sm font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-300 dark:hover:bg-zinc-800" wire:navigate>
                    Back to Home
                </a>
            </div>
        </div>
    </section>
</div>
