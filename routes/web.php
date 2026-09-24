<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('feed', 'pages::feed')->name('feed');

    Route::livewire('u/{user:username}', 'pages::timeline')->name('profile.show');

    Route::livewire('u/{user:username}/friends', 'pages::friends')->name('profile.friends');

    Route::livewire('friend-requests', 'pages::friend-requests')->name('friend-requests');

    Route::livewire('notifications', 'pages::notifications')->name('notifications');

    Route::livewire('messages/{user:username?}', 'pages::messenger')->name('messages');
});

require __DIR__.'/settings.php';
