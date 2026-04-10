<?php

use App\Models\RoomCategory;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Add Room Category')] class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $description = '';
    public string $price_per_night = '';
    public int $max_capacity = 2;
    public string $room_size_sqm = '';
    public int $base_occupancy = 2;
    public string $extra_person_charge = '0';
    public array $amenities = [];
    public bool $is_active = true;
    public string $newAmenity = '';

    #[Validate('nullable|image|max:2048')]
    public $image = null;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    #[Validate(['gallery.*' => 'image|max:2048'])]
    public array $gallery = [];

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

    public function removeGalleryImage(int $index): void
    {
        unset($this->gallery[$index]);
        $this->gallery = array_values($this->gallery);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:room_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'room_size_sqm' => ['nullable', 'integer', 'min:1'],
            'base_occupancy' => ['required', 'integer', 'min:1', 'lte:max_capacity'],
            'extra_person_charge' => ['required', 'numeric', 'min:0'],
            'amenities' => ['array'],
            'is_active' => ['boolean'],
            'gallery' => ['array', 'max:5'],
        ]);

        if ($this->image) {
            $validated['image_path'] = $this->image->store('room-categories', 'public');
        }

        $category = RoomCategory::create($validated);

        foreach ($this->gallery as $index => $galleryImage) {
            $path = $galleryImage->store('room-categories/gallery', 'public');
            $category->images()->create(['image_path' => $path, 'sort_order' => $index]);
        }

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
                <flux:input wire:model="room_size_sqm" label="Room Size (sqm)" type="number" min="1" placeholder="e.g. 30" />
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:input wire:model="max_capacity" label="Max Capacity" type="number" min="1" max="20" required />
                <flux:input wire:model="base_occupancy" label="Base Occupancy" type="number" min="1" max="20" required description="Guests included in base price" />
                <flux:input wire:model="extra_person_charge" label="Extra Person (₱)" type="number" step="0.01" min="0" required description="Charge per additional guest" />
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

            {{-- Cover Image --}}
            <flux:field>
                <flux:label>Cover Image</flux:label>
                <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:text-zinc-400 dark:file:bg-blue-900/20 dark:file:text-blue-400" />
                <flux:error name="image" />
                @if($image)
                    <div class="mt-2">
                        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="h-32 w-auto rounded-lg object-cover" />
                    </div>
                @endif
            </flux:field>

            {{-- Gallery Images --}}
            <flux:field>
                <flux:label>Gallery Images <span class="text-xs font-normal text-zinc-400">(up to 5)</span></flux:label>
                @if(count($gallery) < 5)
                    <input type="file" wire:model="gallery" accept="image/*" multiple class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:text-zinc-400 dark:file:bg-blue-900/20 dark:file:text-blue-400" />
                @endif
                <flux:error name="gallery.*" />
                @if(count($gallery))
                    <div class="mt-2 flex flex-wrap gap-3">
                        @foreach($gallery as $index => $img)
                            <div class="group relative">
                                <img src="{{ $img->temporaryUrl() }}" alt="Gallery preview" class="h-24 w-24 rounded-lg object-cover" />
                                <button type="button" wire:click="removeGalleryImage({{ $index }})" class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white opacity-0 transition group-hover:opacity-100">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">Create Category</flux:button>
                <flux:button variant="ghost" :href="route('room-categories.index')" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
