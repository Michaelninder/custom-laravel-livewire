<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\UserPreferences;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/@{handle}', function ($handle) {
    return view('profile', ['handle' => $handle]);
})->name('profile.handle');

Route::get('/profile/{user}', function (\App\Models\User $user) {
    return view('profile', ['user' => $user]);
})->name('profile.id');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/preferences', UserPreferences::class)->name('settings.preferences');

    // Route::get('support/tickets', []])->name('support.tickets.index');
    // Route::get('support/tickets/{ticket}', [])->name('support.tickets.show');
});

require __DIR__.'/auth.php';