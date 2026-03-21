<?php

use App\Models\RoomCategory;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Add Room Category')] class extends Component {
    public string $name = '';
    public string $description = '';
    public string $price_per_night = '';
    public int $max_capacity = 2;
    public array $amenities = [];
    public bool $is_active = true;
    public string $newAmenity = '';

    public function addAmenity(): void
    {
        $amenity = trim($this->newAmenity);

        if ($amenity !== '' && ! in_array($amenity, $this->amenities)) {
            $this->amenities[] = $amenity;
        }

        $this->newAmenity = '';
    }

    public function removeAmenity(int $index): void
    {
        unset($this->amenities[$index]);
        $this->amenities = array_values($this->amenities);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:room_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'amenities' => ['array'],
            'is_active' => ['boolean'],
        ]);

        RoomCategory::create($validated);

        session()->flash('success', 'Room category created successfully.');
        $this->redirect(route('room-categories.index'), navigate: true);
    }
}; ?>

<div>
    <div class="max-w-2xl space-y-6">
        <flux:heading size="xl">{{ __('Add Room Category') }}</flux:heading>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="name" label="Category Name" placeholder="e.g. Deluxe Suite" required />

            <flux:textarea wire:model="description" label="Description" placeholder="Describe the room category..." rows="3" />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="price_per_night" label="Price per Night (₱)" type="number" step="0.01" min="0" required />
                <flux:input wire:model="max_capacity" label="Max Capacity (Guests)" type="number" min="1" max="20" required />
            </div>

            {{-- Amenities --}}
            <div>
                <flux:field>
                    <flux:label>Amenities</flux:label>
                    <div class="flex gap-2">
                        <flux:input wire:model="newAmenity" placeholder="Add amenity..." wire:keydown.enter.prevent="addAmenity" class="flex-1" />
                        <flux:button type="button" wire:click="addAmenity" variant="ghost">Add</flux:button>
                    </div>
                </flux:field>
                @if(count($amenities))
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($amenities as $index => $amenity)
                            <flux:badge color="blue">
                                {{ $amenity }}
                                <button type="button" wire:click="removeAmenity({{ $index }})" class="ml-1 text-xs">&times;</button>
                            </flux:badge>
                        @endforeach
                    </div>
                @endif
            </div>

            <flux:switch wire:model="is_active" label="Active" description="Guests can see and book this category" />

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">Create Category</flux:button>
                <flux:button variant="ghost" :href="route('room-categories.index')" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
