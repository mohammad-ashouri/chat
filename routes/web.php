<?php

use App\Livewire\Catalogs\IranicMediaTypes;
use App\Livewire\Catalogs\Roles;
use App\Livewire\Catalogs\Sliders;
use App\Livewire\Comments;
use App\Livewire\ContactUs;
use App\Livewire\Dashboard;
use App\Livewire\FileManager\Create as FileManagerCreate;
use App\Livewire\FileManager\Index as FileManagerIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\User\Profile;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/profile', Profile::class)
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/settings', SettingsIndex::class)->name('settings');
});

require __DIR__ . '/auth.php';
