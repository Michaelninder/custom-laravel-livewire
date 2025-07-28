<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\UserPreferences;
use Illuminate\Support\Facades\Route;
use App\Livewire\Support\TicketsIndex;
use App\Livewire\Support\CreateTicket;
use App\Livewire\Support\TicketShow;
use App\Http\Controllers\UserProfileController;
//use App\Livewire\Admin\SiteSettings as AdminSiteSettings;
//use App\Livewire\Admin\Dashboard as AdminDashboard;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


    Route::get('/@{handle}', [UserProfileController::class, 'showByHandle'])->name('profile.handle');
    Route::get('/profile/{user}', [UserProfileController::class, 'showById'])->name('profile.id');
    Route::get('/profile/username/{user:username}', [UserProfileController::class, 'showByUsername'])->name('profile.username');
    Route::get('/user/{user:username}', [UserProfileController::class, 'showByUsername']);
    Route::get('/user/{handle}', [UserProfileController::class, 'showByHandle']);


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/preferences', UserPreferences::class)->name('settings.preferences');

    Route::prefix('support')->name('support.')->group(function () {
        Route::get('tickets', TicketsIndex::class)->name('tickets.index');
        Route::get('tickets/create', CreateTicket::class)->name('tickets.create');
        Route::get('tickets/{ticket}', TicketShow::class)->name('tickets.show');
    });


    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        //Route::get('dashboard', AdminDashboard::class)->name('dashboard');
        //Route::get('settings', AdminSiteSettings::class)->name('settings');
        Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('settings', \App\Livewire\Admin\SiteSettings::class)->name('settings');
    });
});

require __DIR__.'/auth.php';