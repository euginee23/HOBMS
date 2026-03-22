<?php

use App\Models\RoomCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Room Details')] class extends Component {
    public RoomCategory $category;

    public function mount(string $slug): void
    {
        $this->category = RoomCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function with(): array
    {
        $availableRooms = $this->category->rooms()->available()->orderBy('room_number')->get();

        return compact('availableRooms');
    }
}; ?>

<div>
    <section class="py-16">
        <div class="mx-auto max-w-4xl px-6">
            {{-- Breadcrumb --}}
            <div class="mb-8">
                <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" wire:navigate>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    Back to Rooms
                </a>
            </div>

            {{-- Header Image --}}
            <div class="flex h-64 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/20 dark:to-zinc-800">
                <svg class="size-24 text-blue-300 dark:text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21"/>
                </svg>
            </div>

            {{-- Details --}}
            <div class="mt-8">
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $category->name }}</h1>
                <p class="mt-4 text-zinc-600 dark:text-zinc-400">{{ $category->description }}</p>

                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Price per Night</div>
                        <div class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($category->price_per_night) }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Max Guests</div>
                        <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $category->max_capacity }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Available Rooms</div>
                        <div class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $availableRooms->count() }}</div>
                    </div>
                </div>

                @if($category->amenities && count($category->amenities))
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Amenities</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($category->amenities as $amenity)
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-sm text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">{{ $amenity }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($availableRooms->isNotEmpty())
                    <div class="mt-10">
                        <a href="{{ route('booking.create', $category->slug) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500" wire:navigate>
                            Book This Room
                        </a>
                    </div>
                @else
                    <div class="mt-10 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-400">
                        No rooms are currently available in this category. Please check back later.
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
