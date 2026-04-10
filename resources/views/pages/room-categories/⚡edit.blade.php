<?php

use App\Models\RoomCategory;
use App\Models\RoomCategoryImage;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Edit Room Category')] class extends Component {
    use WithFileUploads;

    public RoomCategory $roomCategory;

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
    #[Validate(['newGallery.*' => 'image|max:2048'])]
    public array $newGallery = [];

    public function mount(RoomCategory $roomCategory): void
    {
        $this->roomCategory = $roomCategory->load('images');
        $this->name = $roomCategory->name;
        $this->description = $roomCategory->description ?? '';
        $this->price_per_night = $roomCategory->price_per_night;
        $this->max_capacity = $roomCategory->max_capacity;
        $this->room_size_sqm = $roomCategory->room_size_sqm ? (string) $roomCategory->room_size_sqm : '';
        $this->base_occupancy = $roomCategory->base_occupancy;
        $this->extra_person_charge = $roomCategory->extra_person_charge;
        $this->amenities = $roomCategory->amenities ?? [];
        $this->is_active = $roomCategory->is_active;
    }

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

    public function removeExistingGalleryImage(int $imageId): void
    {
        $image = RoomCategoryImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        $this->roomCategory->load('images');
    }

    public function removeNewGalleryImage(int $index): void
    {
        unset($this->newGallery[$index]);
        $this->newGallery = array_values($this->newGallery);
    }

    public function save(): void
    {
        $existingCount = $this->roomCategory->images()->count();
        $newCount = count($this->newGallery);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:room_categories,name,' . $this->roomCategory->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'room_size_sqm' => ['nullable', 'integer', 'min:1'],
            'base_occupancy' => ['required', 'integer', 'min:1', 'lte:max_capacity'],
            'extra_person_charge' => ['required', 'numeric', 'min:0'],
            'amenities' => ['array'],
            'is_active' => ['boolean'],
        ]);

        if (($existingCount + $newCount) > 5) {
            $this->addError('newGallery', 'Maximum 5 gallery images allowed. You have ' . $existingCount . ' existing.');

            return;
        }

        if ($this->image) {
            if ($this->roomCategory->image_path && Storage::disk('public')->exists($this->roomCategory->image_path)) {
                Storage::disk('public')->delete($this->roomCategory->image_path);
            }

            $validated['image_path'] = $this->image->store('room-categories', 'public');
        }

        $this->roomCategory->update($validated);

        foreach ($this->newGallery as $index => $galleryImage) {
            $path = $galleryImage->store('room-categories/gallery', 'public');
            $this->roomCategory->images()->create([
                'image_path' => $path,
                'sort_order' => $existingCount + $index,
            ]);
        }

        session()->flash('success', 'Room category updated successfully.');
        $this->redirect(route('room-categories.index'), navigate: true);
    }
}; ?>

<div>
    <div class="max-w-2xl space-y-6">
        <flux:heading size="xl">{{ __('Edit Room Category') }}</flux:heading>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="name" label="Category Name" required />

            <flux:textarea wire:model="description" label="Description" rows="3" />

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
                @if(!$image && $roomCategory->image_path)
                    <div class="mb-2">
                        <img src="{{ Storage::url($roomCategory->image_path) }}" alt="{{ $roomCategory->name }}" class="h-32 w-auto rounded-lg object-cover" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Current cover image</p>
                    </div>
                @endif
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
                @if($roomCategory->images->count())
                    <div class="mb-2 flex flex-wrap gap-3">
                        @foreach($roomCategory->images as $galleryImg)
                            <div class="group relative">
                                <img src="{{ Storage::url($galleryImg->image_path) }}" alt="Gallery" class="h-24 w-24 rounded-lg object-cover" />
                                <button type="button" wire:click="removeExistingGalleryImage({{ $galleryImg->id }})" wire:confirm="Remove this gallery image?" class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white opacity-0 transition group-hover:opacity-100">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(($roomCategory->images->count() + count($newGallery)) < 5)
                    <input type="file" wire:model="newGallery" accept="image/*" multiple class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:text-zinc-400 dark:file:bg-blue-900/20 dark:file:text-blue-400" />
                @endif
                <flux:error name="newGallery" />
                @if(count($newGallery))
                    <div class="mt-2 flex flex-wrap gap-3">
                        @foreach($newGallery as $index => $img)
                            <div class="group relative">
                                <img src="{{ $img->temporaryUrl() }}" alt="New gallery" class="h-24 w-24 rounded-lg object-cover" />
                                <button type="button" wire:click="removeNewGalleryImage({{ $index }})" class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white opacity-0 transition group-hover:opacity-100">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">Update Category</flux:button>
                <flux:button variant="ghost" :href="route('room-categories.index')" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
