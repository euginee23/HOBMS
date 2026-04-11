<?php

use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\Complaint;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Validate('required|string|exists:bookings,booking_reference')]
    public string $booking_reference = '';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|max:2000')]
    public string $description = '';

    public function submitComplaint(): void
    {
        $validated = $this->validate();

        $booking = Booking::where('booking_reference', $validated['booking_reference'])->first();

        if (! $booking) {
            $this->addError('booking_reference', 'Booking reference was not found.');

            return;
        }

        Complaint::create([
            'booking_id' => $booking->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'complaint_status' => ComplaintStatus::Open,
        ]);

        $this->reset('booking_reference', 'subject', 'description');

        session()->flash('complaint_success', 'Complaint submitted successfully. Our team will review it as soon as possible.');
    }
}; ?>

<div>
    @if(session('complaint_success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('complaint_success') }}
        </flux:callout>
    @endif

    <form wire:submit="submitComplaint" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <flux:field>
            <flux:label>Booking Reference</flux:label>
            <flux:input wire:model="booking_reference" placeholder="e.g. BK-20260412-0001" required />
            <flux:error name="booking_reference" />
        </flux:field>

        <flux:field>
            <flux:label>Subject</flux:label>
            <flux:input wire:model="subject" placeholder="Short complaint subject" required />
            <flux:error name="subject" />
        </flux:field>

        <flux:field>
            <flux:label>Complaint Details</flux:label>
            <flux:textarea wire:model="description" rows="5" placeholder="Describe your concern in detail..." required />
            <flux:error name="description" />
        </flux:field>

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" icon="paper-airplane">Submit Complaint</flux:button>
        </div>
    </form>
</div>
