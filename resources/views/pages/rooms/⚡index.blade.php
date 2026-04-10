<?php

use App\Models\RoomCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Our Rooms')] #[Layout('layouts.public')] class extends Component {
    public function with(): array
    {
        $categories = RoomCategory::active()
            ->withCount(['rooms' => fn ($q) => $q->available()])
            ->get();

        return compact('categories');
    }
}; ?>

<div>
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">Our Rooms</h1>
                <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Explore our room categories and find the perfect fit for your stay</p>
            </div>

            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($categories as $category)
                    <div class="group overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="relative h-48 overflow-hidden">
                            @if($category->image_path)
                                <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                            @else
                                <div class="flex h-full items-center justify-center bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/20 dark:to-zinc-800">
                                    <svg class="size-16 text-blue-300 dark:text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $category->name }}</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ Str::limit($category->description, 120) }}</p>

                            @if($category->amenities && count($category->amenities))
                                <div class="mt-3 flex flex-wrap gap-1">
                                    @foreach(array_slice($category->amenities, 0, 4) as $amenity)
                                        <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">{{ $amenity }}</span>
                                    @endforeach
                                    @if(count($category->amenities) > 4)
                                        <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-500 dark:bg-zinc-800 dark:text-zinc-500">+{{ count($category->amenities) - 4 }} more</span>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-3 flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                                @if($category->room_size_sqm)
                                    <span>{{ $category->room_size_sqm }} sqm</span>
                                    <span>&middot;</span>
                                @endif
                                <span>{{ $category->base_occupancy }}–{{ $category->max_capacity }} guests</span>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($category->price_per_night) }}</span>
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">/night</span>
                                </div>
                                <div class="text-right text-sm text-zinc-500 dark:text-zinc-400">
                                    <div>{{ $category->rooms_count }} available</div>
                                </div>
                            </div>
                            <a href="{{ route('rooms.show', $category->slug) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500" wire:navigate>
                                View Details & Book
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-zinc-500">
                        <p class="text-lg">No rooms available at the moment.</p>
                        <p class="mt-2 text-sm">Please check back later.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
