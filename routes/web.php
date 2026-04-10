<?php

use Illuminate\Support\Facades\Route;

// Public pages
Route::view('/', 'welcome')->name('home');
Route::livewire('forgot-password', 'pages::auth.forgot-password')->name('password.request')->middleware('guest');
Route::livewire('rooms', 'pages::rooms.index')->name('rooms.index');
Route::livewire('rooms/{slug}', 'pages::rooms.show')->name('rooms.show');
Route::livewire('book/{slug}', 'pages::booking.create')->name('booking.create');
Route::livewire('booking/confirmation/{token}', 'pages::booking.confirmation')->name('booking.confirmation');

// Guest portal
Route::livewire('portal', 'pages::portal.lookup')->name('portal.lookup');
Route::livewire('portal/view/{token}', 'pages::portal.view')->name('portal.view');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    // All staff (admin + receptionist)
    Route::livewire('bookings', 'pages::bookings.index')->name('bookings.index');
    Route::livewire('bookings/create', 'pages::bookings.create')->name('bookings.create');
    Route::livewire('bookings/{booking}', 'pages::bookings.show')->name('bookings.show');
    Route::livewire('payments', 'pages::payments.index')->name('payments.index');
    Route::livewire('payments/{booking}/create', 'pages::payments.create')->name('payments.create');

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::livewire('room-categories', 'pages::room-categories.index')->name('room-categories.index');
        Route::livewire('room-categories/create', 'pages::room-categories.create')->name('room-categories.create');
        Route::livewire('room-categories/{roomCategory}/edit', 'pages::room-categories.edit')->name('room-categories.edit');
        Route::livewire('rooms-manage', 'pages::rooms-manage.index')->name('rooms-manage.index');
        Route::livewire('rooms-manage/create', 'pages::rooms-manage.create')->name('rooms-manage.create');
        Route::livewire('rooms-manage/{room}/edit', 'pages::rooms-manage.edit')->name('rooms-manage.edit');
        Route::livewire('complaints', 'pages::complaints.index')->name('complaints.index');
        Route::livewire('complaints/{complaint}', 'pages::complaints.show')->name('complaints.show');
        Route::livewire('reports', 'pages::reports.index')->name('reports.index');
        Route::livewire('staff', 'pages::staff.index')->name('staff.index');
        Route::livewire('staff/create', 'pages::staff.create')->name('staff.create');
        Route::livewire('staff/{user}/edit', 'pages::staff.edit')->name('staff.edit');
    });
});

require __DIR__.'/settings.php';
