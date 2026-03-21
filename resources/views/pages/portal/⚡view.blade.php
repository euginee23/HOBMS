<?php

use App\Enums\BookingStatus;
use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\Complaint;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component {
    public Booking $booking;

    public string $subject = '';
    public string $description = '';
    public bool $showComplaintForm = false;

    public function mount(string $token): void
    {
        $this->booking = Booking::where('portal_token', $token)
            ->with(['room.roomCategory', 'payments', 'complaints'])
            ->firstOrFail();
    }

    public function title(): string
    {
        return 'Booking ' . $this->booking->booking_reference;
    }

    public function submitComplaint(): void
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        Complaint::create([
            'booking_id' => $this->booking->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'complaint_status' => ComplaintStatus::Open,
        ]);

        $this->reset('subject', 'description', 'showComplaintForm');
        $this->booking->load('complaints');

        session()->flash('complaint_success', 'Your complaint has been submitted. We will respond as soon as possible.');
    }
}; ?>

<div>
    <section class="py-16">
        <div class="mx-auto max-w-3xl px-6">
            <div class="mb-8">
                <a href="{{ route('portal.lookup') }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400" wire:navigate>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    Track Another Booking
                </a>
            </div>

            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $booking->booking_reference }}</h1>
                    <p class="mt-1 text-zinc-600 dark:text-zinc-400">Booked by {{ $booking->guest_name }}</p>
                </div>
                <flux:badge :color="$booking->booking_status->color()" size="lg">{{ $booking->booking_status->label() }}</flux:badge>
            </div>

            {{-- Booking Details --}}
            <div class="mt-8 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Booking Details</h2>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Room Type</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $booking->room->roomCategory->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Room Number</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $booking->room->room_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Check-in</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $booking->check_in_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Check-out</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $booking->check_out_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Duration</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $booking->nights }} {{ Str::plural('night', $booking->nights) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Guests</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $booking->num_guests }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Payment Summary --}}
            <div class="mt-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Payment Summary</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Payment Status</dt>
                        <dd><flux:badge :color="$booking->payment_status->color()">{{ $booking->payment_status->label() }}</flux:badge></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Total Amount</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">₱{{ number_format($booking->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Amount Paid</dt>
                        <dd class="font-medium text-green-600 dark:text-green-400">₱{{ number_format($booking->amount_paid, 2) }}</dd>
                    </div>
                    @if($booking->balance_remaining > 0)
                        <div class="flex justify-between border-t border-zinc-200 pt-2 dark:border-zinc-700">
                            <dt class="font-semibold text-zinc-900 dark:text-white">Balance Remaining</dt>
                            <dd class="font-bold text-red-600 dark:text-red-400">₱{{ number_format($booking->balance_remaining, 2) }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Complaints Section --}}
            <div class="mt-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Complaints</h2>
                    @if(in_array($booking->booking_status, [BookingStatus::Confirmed, BookingStatus::CheckedIn, BookingStatus::CheckedOut]))
                        <button wire:click="$toggle('showComplaintForm')" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                            {{ $showComplaintForm ? 'Cancel' : '+ Submit Complaint' }}
                        </button>
                    @endif
                </div>

                @if(session('complaint_success'))
                    <div class="mt-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
                        {{ session('complaint_success') }}
                    </div>
                @endif

                @if($showComplaintForm)
                    <form wire:submit="submitComplaint" class="mt-4 space-y-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                        <flux:input wire:model="subject" label="Subject" placeholder="Brief description of your complaint" required />
                        <flux:textarea wire:model="description" label="Description" placeholder="Please describe your complaint in detail..." rows="4" required />
                        <flux:button type="submit" variant="primary" size="sm">Submit Complaint</flux:button>
                    </form>
                @endif

                @if($booking->complaints->count())
                    <div class="mt-4 space-y-4">
                        @foreach($booking->complaints->sortByDesc('created_at') as $complaint)
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $complaint->subject }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $complaint->complaint_reference }} &middot; {{ $complaint->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <flux:badge :color="$complaint->complaint_status->color()" size="sm">{{ $complaint->complaint_status->label() }}</flux:badge>
                                </div>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $complaint->description }}</p>
                                @if($complaint->admin_response)
                                    <div class="mt-3 rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                                        <p class="text-xs font-medium text-blue-800 dark:text-blue-400">Admin Response:</p>
                                        <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">{{ $complaint->admin_response }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif(!$showComplaintForm)
                    <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">No complaints submitted.</p>
                @endif
            </div>
        </div>
    </section>
</div>
