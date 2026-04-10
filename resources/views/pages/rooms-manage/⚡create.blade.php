<?php

use App\Enums\BedType;
use App\Enums\RoomStatus;
use App\Enums\ViewType;
use App\Models\Room;
use App\Models\RoomCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Add Room')] class extends Component {
    public string $room_number = '';
    public string $room_category_id = '';
    public int $floor = 1;
    public string $bed_type = '';
    public int $bed_count = 1;
    public string $view_type = 'none';
    public bool $is_smoking = false;
    public string $status = 'available';
    public string $notes = '';

    public function save(): void
    {
        $validated = $this->validate([
            'room_number' => ['required', 'string', 'max:20', 'unique:rooms,room_number'],
            'room_category_id' => ['required', 'exists:room_categories,id'],
            'floor' => ['required', 'integer', 'min:1'],
            'bed_type' => ['nullable', 'in:' . implode(',', array_column(BedType::cases(), 'value'))],
            'bed_count' => ['required', 'integer', 'min:1', 'max:10'],
            'view_type' => ['required', 'in:' . implode(',', array_column(ViewType::cases(), 'value'))],
            'is_smoking' => ['boolean'],
            'status' => ['required', 'in:' . implode(',', array_column(RoomStatus::cases(), 'value'))],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Room::create($validated);

        session()->flash('success', 'Room created successfully.');
        $this->redirect(route('rooms-manage.index'), navigate: true);
    }

    #[Computed]
    public function categories()
    {
        return RoomCategory::query()->orderBy('name')->get();
    }
}; ?>

<div>
    <div class="max-w-2xl space-y-6">
        <flux:heading size="xl">{{ __('Add Room') }}</flux:heading>

        <form wire:submit="save" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="room_number" label="Room Number" placeholder="e.g. 101" required />
                <flux:input wire:model="floor" label="Floor" type="number" min="1" required />
            </div>

            <flux:select wire:model="room_category_id" label="Category" required>
                <flux:select.option value="">Select Category</flux:select.option>
                @foreach($this->categories as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }} (₱{{ number_format($cat->price_per_night, 2) }}/night)</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:select wire:model="bed_type" label="Bed Type">
                    <flux:select.option value="">Select Bed Type</flux:select.option>
                    @foreach(BedType::cases() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="bed_count" label="Number of Beds" type="number" min="1" max="10" required />

                <flux:select wire:model="view_type" label="View Type" required>
                    @foreach(ViewType::cases() as $view)
                        <flux:select.option value="{{ $view->value }}">{{ $view->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:select wire:model="status" label="Status">
                    @foreach(RoomStatus::cases() as $status)
                        <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="flex items-end pb-1">
                    <flux:switch wire:model="is_smoking" label="Smoking Allowed" />
                </div>
            </div>

            <flux:textarea wire:model="notes" label="Notes" placeholder="Optional notes about this room..." rows="2" />

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">Create Room</flux:button>
                <flux:button variant="ghost" :href="route('rooms-manage.index')" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
