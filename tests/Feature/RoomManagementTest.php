<?php

use App\Enums\BedType;
use App\Enums\ViewType;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Livewire\Livewire;

test('admin can view rooms index', function () {
    $admin = User::factory()->admin()->create();
    $category = RoomCategory::factory()->create();
    Room::factory()->create(['room_category_id' => $category->id, 'room_number' => '101']);

    $this->actingAs($admin)
        ->get(route('rooms-manage.index'))
        ->assertOk()
        ->assertSee('101');
});

test('admin can create a room with new fields', function () {
    $admin = User::factory()->admin()->create();
    $category = RoomCategory::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::rooms-manage.create')
        ->set('room_number', '201')
        ->set('room_category_id', (string) $category->id)
        ->set('floor', 2)
        ->set('bed_type', 'queen')
        ->set('bed_count', 1)
        ->set('view_type', 'ocean')
        ->set('is_smoking', false)
        ->call('save')
        ->assertRedirect(route('rooms-manage.index'));

    $this->assertDatabaseHas('rooms', [
        'room_number' => '201',
        'bed_type' => 'queen',
        'bed_count' => 1,
        'view_type' => 'ocean',
        'is_smoking' => false,
    ]);
});

test('admin can edit room with new fields', function () {
    $admin = User::factory()->admin()->create();
    $category = RoomCategory::factory()->create();
    $room = Room::factory()->create([
        'room_category_id' => $category->id,
        'bed_type' => BedType::Double,
        'view_type' => ViewType::City,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::rooms-manage.edit', ['room' => $room])
        ->set('bed_type', 'king')
        ->set('bed_count', 1)
        ->set('view_type', 'ocean')
        ->set('is_smoking', true)
        ->call('save')
        ->assertRedirect(route('rooms-manage.index'));

    $room->refresh();
    expect($room->bed_type)->toBe(BedType::King)
        ->and($room->view_type)->toBe(ViewType::Ocean)
        ->and($room->is_smoking)->toBeTrue();
});

test('rooms index shows bed type and view columns', function () {
    $admin = User::factory()->admin()->create();
    $category = RoomCategory::factory()->create();
    Room::factory()->create([
        'room_category_id' => $category->id,
        'room_number' => '301',
        'bed_type' => BedType::King,
        'view_type' => ViewType::Ocean,
    ]);

    $this->actingAs($admin)
        ->get(route('rooms-manage.index'))
        ->assertOk()
        ->assertSee('King')
        ->assertSee('Ocean View');
});

test('receptionist cannot access room management', function () {
    $receptionist = User::factory()->receptionist()->create();

    $this->actingAs($receptionist)
        ->get(route('rooms-manage.index'))
        ->assertForbidden();
});
