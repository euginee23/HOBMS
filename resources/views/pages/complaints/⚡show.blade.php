<?php

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Complaint Details')] class extends Component {
    public Complaint $complaint;

    public string $admin_response = '';
    public string $new_status = '';

    public function mount(Complaint $complaint): void
    {
        $this->complaint = $complaint->load(['booking', 'resolvedBy']);
        $this->admin_response = $complaint->admin_response ?? '';
        $this->new_status = $complaint->complaint_status->value;
    }

    public function respond(): void
    {
        $this->validate([
            'admin_response' => ['required', 'string', 'max:2000'],
            'new_status' => ['required', 'in:' . implode(',', array_column(ComplaintStatus::cases(), 'value'))],
        ]);

        $data = [
            'admin_response' => $this->admin_response,
            'complaint_status' => $this->new_status,
        ];

        if (in_array($this->new_status, [ComplaintStatus::Resolved->value, ComplaintStatus::Closed->value])) {
            $data['resolved_by'] = Auth::id();
            $data['resolved_at'] = now();
        }

        $this->complaint->update($data);
        $this->complaint->refresh();

        session()->flash('success', 'Response saved.');
    }
}; ?>

<div>
    <div class="space-y-6">
        <div>
            <flux:button variant="ghost" size="sm" :href="route('complaints.index')" wire:navigate icon="arrow-left">Back to Complaints</flux:button>
            <flux:heading size="xl" class="mt-2">{{ $complaint->complaint_reference }}</flux:heading>
        </div>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Complaint Details --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">Complaint Details</flux:heading>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Status</dt>
                        <dd><flux:badge :color="$complaint->complaint_status->color()">{{ $complaint->complaint_status->label() }}</flux:badge></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Guest</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $complaint->booking->guest_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Booking</dt>
                        <dd>
                            <a href="{{ route('bookings.show', $complaint->booking) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>{{ $complaint->booking->booking_reference }}</a>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Submitted</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $complaint->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($complaint->resolvedBy)
                        <div class="flex justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Resolved By</dt>
                            <dd class="text-zinc-900 dark:text-white">{{ $complaint->resolvedBy->name }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6">
                    <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Subject</h4>
                    <p class="mt-1 text-zinc-900 dark:text-white">{{ $complaint->subject }}</p>
                </div>

                <div class="mt-4">
                    <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Description</h4>
                    <p class="mt-1 whitespace-pre-line text-zinc-900 dark:text-white">{{ $complaint->description }}</p>
                </div>
            </div>

            {{-- Admin Response --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">Admin Response</flux:heading>

                <form wire:submit="respond" class="space-y-4">
                    <flux:select wire:model="new_status" label="Update Status" required>
                        @foreach(ComplaintStatus::cases() as $status)
                            <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:textarea wire:model="admin_response" label="Response" placeholder="Write your response to the guest..." rows="6" required />

                    <flux:button type="submit" variant="primary">Save Response</flux:button>
                </form>
            </div>
        </div>
    </div>
</div>
