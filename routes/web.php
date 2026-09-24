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

    Route::livewire('groups', 'pages::groups')->name('groups');

    Route::livewire('groups/create', 'pages::group-create')->name('groups.create');

    Route::livewire('g/{group:slug}', 'pages::group')->name('groups.show');

    Route::livewire('g/{group:slug}/members', 'pages::group-members')->name('groups.members');

    Route::livewire('pages', 'pages::pages')->name('pages');

    Route::livewire('pages/create', 'pages::page-create')->name('pages.create');

    Route::livewire('p/{page:slug}', 'pages::page')->name('pages.show');

    Route::livewire('p/{page:slug}/manage', 'pages::page-manage')->name('pages.manage');

    Route::livewire('messages/{user:username?}', 'pages::messenger')->name('messages');
});

require __DIR__.'/settings.php';
