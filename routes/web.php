<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']))->name('health');

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

    Route::livewire('jobs', 'pages::jobs')->name('jobs');

    Route::livewire('jobs/create', 'pages::job-create')->name('jobs.create');

    Route::livewire('jobs/{job:slug}', 'pages::job-show')->name('jobs.show');

    Route::livewire('jobs/{job:slug}/manage', 'pages::job-manage')->name('jobs.manage');

    Route::livewire('jobs/{job:slug}/applicants', 'pages::job-applicants')->name('jobs.applicants');

    Route::livewire('forum', 'pages::forum')->name('forum');

    // Declared before the category routes so a category can never shadow "topic".
    Route::livewire('forum/topic/{topic:slug}', 'pages::forum-topic')->name('forum.topic');

    Route::livewire('forum/{category:slug}', 'pages::forum-category')->name('forum.category');

    Route::livewire('forum/{category:slug}/create', 'pages::forum-topic-create')->name('forum.topic.create');

    Route::livewire('marketplace', 'pages::marketplace')->name('marketplace');

    Route::livewire('marketplace/create', 'pages::listing-create')->name('marketplace.create');

    // Declared before the show route so "edit" can never be read as a slug.
    Route::livewire('marketplace/{listing:slug}/edit', 'pages::listing-edit')->name('marketplace.listing.edit');

    Route::livewire('marketplace/{listing:slug}', 'pages::listing')->name('marketplace.listing');

    Route::livewire('pittube', 'pages::videos')->name('videos');

    Route::livewire('pittube/channels/create', 'pages::channel-create')->name('videos.channel.create');

    // Declared before the show route so "watch" can never be read as a slug.
    Route::livewire('pittube/watch/{video:slug}', 'pages::video')->name('videos.show');

    Route::livewire('pittube/{video:slug}/edit', 'pages::video-edit')->name('videos.edit');

    Route::livewire('pittube/channel/{channel:slug}', 'pages::channel')->name('videos.channel');

    Route::livewire('pittube/channel/{channel:slug}/manage', 'pages::channel-manage')->name('videos.channel.manage');

    Route::livewire('pittube/channel/{channel:slug}/upload', 'pages::video-upload')->name('videos.upload');
});

require __DIR__.'/settings.php';
