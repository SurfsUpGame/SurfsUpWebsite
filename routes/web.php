<?php

use App\Http\Controllers\PublicRoadmapController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('home');
})->name('home');

//Route::view('dashboard', 'dashboard')
//    ->middleware(['auth', 'verified'])
//    ->name('dashboard');
//
//Route::middleware(['auth'])->group(function () {
//    Route::redirect('settings', 'settings/profile');
//
//    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
//    Volt::route('settings/password', 'settings.password')->name('settings.password');
//    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
//});

// Route::get('login', \App\Http\Controllers\Auth\SteamAuthController::class)->name('login');
Route::get('/roadmap', function () {
    return view('roadmap');
});


// require __DIR__.'/auth.php';
