<?php

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Complaints')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function complaints()
    {
        return Complaint::query()
            ->with('booking')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('complaint_reference', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%")
                    ->orWhereHas('booking', fn ($bq) => $bq->where('guest_name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('complaint_status', $this->statusFilter))
            ->latest()
            ->paginate(15);
    }
}; ?>

<div>
    <div class="space-y-6">
        <flux:heading size="xl">{{ __('Complaints') }}</flux:heading>

        <div class="flex flex-wrap gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce="search" placeholder="Search reference, subject, guest..." icon="magnifying-glass" />
            </div>
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach(ComplaintStatus::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Reference</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Subject</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Guest</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Booking</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Date</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->complaints as $complaint)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $complaint->complaint_reference }}</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">{{ Str::limit($complaint->subject, 40) }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $complaint->booking->guest_name }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('bookings.show', $complaint->booking) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                                    {{ $complaint->booking->booking_reference }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" :color="$complaint->complaint_status->color()">{{ $complaint->complaint_status->label() }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $complaint->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <flux:button variant="ghost" size="sm" :href="route('complaints.show', $complaint)" wire:navigate>View</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">No complaints found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $this->complaints->links() }}</div>
    </div>
</div>
