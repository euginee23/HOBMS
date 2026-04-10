<?php

use App\Enums\RoomStatus;
use App\Models\Room;
use App\Models\RoomCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Rooms Management')] class extends Component {
    public string $search = '';
    public string $statusFilter = '';
    public string $categoryFilter = '';

    public function deleteRoom(int $id): void
    {
        $room = Room::findOrFail($id);

        if ($room->bookings()->exists()) {
            session()->flash('error', 'Cannot delete room with existing bookings.');

            return;
        }

        $room->delete();
        session()->flash('success', 'Room deleted.');
    }

    #[Computed]
    public function rooms()
    {
        return Room::query()
            ->with('roomCategory')
            ->when($this->search, fn ($q) => $q->where('room_number', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter, fn ($q) => $q->where('room_category_id', $this->categoryFilter))
            ->orderBy('room_number')
            ->get();
    }

    #[Computed]
    public function categories()
    {
        return RoomCategory::query()->orderBy('name')->get();
    }
}; ?>

<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Rooms') }}</flux:heading>
            <flux:button variant="primary" :href="route('rooms-manage.create')" wire:navigate>
                {{ __('Add Room') }}
            </flux:button>
        </div>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif
        @if(session('error'))
            <flux:callout variant="danger" icon="x-circle">{{ session('error') }}</flux:callout>
        @endif

        <div class="flex flex-wrap gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce="search" placeholder="Search room number..." icon="magnifying-glass" />
            </div>
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach(RoomStatus::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="categoryFilter" placeholder="All Categories">
                <flux:select.option value="">All Categories</flux:select.option>
                @foreach($this->categories as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Room #</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Category</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Floor</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Bed</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">View</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->rooms as $room)
                        <tr wire:key="room-{{ $room->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $room->room_number }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $room->roomCategory->name }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $room->floor }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $room->bed_type?->label() ?? '—' }} {{ $room->bed_count > 1 ? '×' . $room->bed_count : '' }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $room->view_type->label() }}</td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" :color="$room->status->color()">{{ $room->status->label() }}</flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <flux:button variant="ghost" size="sm" :href="route('rooms-manage.edit', $room)" wire:navigate>Edit</flux:button>
                                    <flux:button variant="ghost" size="sm" wire:click="deleteRoom({{ $room->id }})" wire:confirm="Are you sure you want to delete this room?">Delete</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">No rooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
