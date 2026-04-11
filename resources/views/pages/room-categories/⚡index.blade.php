<?php

use App\Models\RoomCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Room Categories')] class extends Component {
    public string $search = '';

    public function deleteCategory(int $id): void
    {
        $category = RoomCategory::findOrFail($id);

        if ($category->rooms()->exists()) {
            session()->flash('error', 'Cannot delete category with existing rooms.');

            return;
        }

        $category->delete();
        session()->flash('success', 'Room category deleted.');
    }

    #[Computed]
    public function categories()
    {
        return RoomCategory::query()
            ->withCount('rooms')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Room Categories') }}</flux:heading>
            <flux:button variant="primary" :href="route('room-categories.create')" wire:navigate>
                {{ __('Add Category') }}
            </flux:button>
        </div>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif
        @if(session('error'))
            <flux:callout variant="danger" icon="x-circle">{{ session('error') }}</flux:callout>
        @endif

        <flux:input wire:model.live.debounce="search" placeholder="Search categories..." icon="magnifying-glass" />

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Name</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Price/Night</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Size</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Occupancy</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Rooms</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-3 font-medium text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->categories as $category)
                        <tr wire:key="cat-{{ $category->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($category->cover_image_url)
                                        <img src="{{ $category->cover_image_url }}" alt="{{ $category->name }}" class="size-10 rounded-lg object-cover" />
                                    @else
                                        <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                            <flux:icon.building-office class="size-5 text-blue-400" />
                                        </div>
                                    @endif
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">₱{{ number_format($category->price_per_night, 2) }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $category->room_size_sqm ? $category->room_size_sqm . ' sqm' : '—' }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $category->base_occupancy }}–{{ $category->max_capacity }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $category->rooms_count }}</td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" :color="$category->is_active ? 'lime' : 'zinc'">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <flux:button variant="ghost" size="sm" :href="route('room-categories.edit', $category)" wire:navigate>Edit</flux:button>
                                    <flux:button variant="ghost" size="sm" wire:click="deleteCategory({{ $category->id }})" wire:confirm="Are you sure you want to delete this category?">Delete</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">No room categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
