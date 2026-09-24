<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('feed', 'pages::feed')->name('feed');

    Route::livewire('u/{user:username}', 'pages::timeline')->name('profile.show');
});

require __DIR__.'/settings.php';
