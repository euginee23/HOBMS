<?php

use App\Models\RoomCategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('admin can view room categories index', function () {
    $admin = User::factory()->admin()->create();
    RoomCategory::factory()->create(['name' => 'Deluxe']);

    $this->actingAs($admin)
        ->get(route('room-categories.index'))
        ->assertOk()
        ->assertSee('Deluxe');
});

test('admin can create a room category with new fields', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::room-categories.create')
        ->set('name', 'Premium')
        ->set('description', 'A premium room with panoramic views')
        ->set('price_per_night', '5000')
        ->set('max_capacity', 4)
        ->set('room_size_sqm', '35')
        ->set('base_occupancy', 2)
        ->set('extra_person_charge', '800')
        ->set('amenities', ['Wi-Fi', 'Air Conditioning', 'Mini Bar'])
        ->set('image', UploadedFile::fake()->image('cover.jpg'))
        ->call('save')
        ->assertRedirect(route('room-categories.index'));

    $this->assertDatabaseHas('room_categories', [
        'name' => 'Premium',
        'room_size_sqm' => 35,
        'base_occupancy' => 2,
        'extra_person_charge' => 800,
    ]);
});

test('base occupancy cannot exceed max capacity', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::room-categories.create')
        ->set('name', 'Test')
        ->set('description', 'Test desc')
        ->set('price_per_night', '3000')
        ->set('max_capacity', 2)
        ->set('base_occupancy', 5)
        ->set('extra_person_charge', '500')
        ->call('save')
        ->assertHasErrors(['base_occupancy']);
});

test('admin can edit room category with new fields', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $category = RoomCategory::factory()->create([
        'name' => 'Standard',
        'max_capacity' => 4,
        'room_size_sqm' => 22,
        'base_occupancy' => 2,
        'extra_person_charge' => 500,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::room-categories.edit', ['roomCategory' => $category])
        ->set('room_size_sqm', '30')
        ->set('base_occupancy', 3)
        ->set('extra_person_charge', '700')
        ->call('save')
        ->assertRedirect(route('room-categories.index'));

    $category->refresh();
    expect($category->room_size_sqm)->toBe(30)
        ->and($category->base_occupancy)->toBe(3)
        ->and((float) $category->extra_person_charge)->toBe(700.0);
});

test('cover_image_url accessor returns url when image exists', function () {
    Storage::fake('public');
    $category = RoomCategory::factory()->create(['image_path' => 'room-categories/test.jpg']);

    expect($category->cover_image_url)->toContain('/storage/room-categories/test.jpg');
});

test('cover_image_url accessor returns null when no image', function () {
    $category = RoomCategory::factory()->create(['image_path' => null]);

    expect($category->cover_image_url)->toBeNull();
});
