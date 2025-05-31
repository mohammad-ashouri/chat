<?php

use App\Livewire\ChatRoom;
use App\Livewire\Dashboard;
use App\Livewire\User\Profile;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/profile', Profile::class)
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/chat', ChatRoom::class)->name('chat');
});

require __DIR__ . '/auth.php';
