<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;

test('admin can view staff index', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('staff.index'))
        ->assertOk();
});

test('receptionist cannot view staff index', function () {
    $receptionist = User::factory()->receptionist()->create();

    $this->actingAs($receptionist)
        ->get(route('staff.index'))
        ->assertForbidden();
});

test('admin can create staff member', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('staff.create'))
        ->assertOk();

    Livewire::actingAs($admin)
        ->test('pages::staff.create')
        ->set('name', 'New Staff')
        ->set('email', 'newstaff@hobms.test')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->set('role', 'receptionist')
        ->call('save')
        ->assertRedirect(route('staff.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'newstaff@hobms.test',
        'role' => 'receptionist',
    ]);
});

test('admin can edit staff member', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->receptionist()->create();

    $this->actingAs($admin)
        ->get(route('staff.edit', $staff))
        ->assertOk();

    Livewire::actingAs($admin)
        ->test('pages::staff.edit', ['user' => $staff])
        ->set('name', 'Updated Name')
        ->set('email', 'updated@hobms.test')
        ->set('role', 'admin')
        ->call('save')
        ->assertRedirect(route('staff.index'));

    $staff->refresh();
    expect($staff->name)->toBe('Updated Name')
        ->and($staff->email)->toBe('updated@hobms.test')
        ->and($staff->role)->toBe(UserRole::Admin);
});

test('admin can delete staff member', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->receptionist()->create();

    Livewire::actingAs($admin)
        ->test('pages::staff.index')
        ->call('confirmDelete', $staff->id)
        ->assertSet('showDeleteModal', true)
        ->call('deleteStaff');

    $this->assertDatabaseMissing('users', ['id' => $staff->id]);
});

test('admin cannot delete own account', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::staff.index')
        ->call('confirmDelete', $admin->id)
        ->call('deleteStaff')
        ->assertSet('showDeleteModal', false);

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
