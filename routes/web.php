<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SteamAuthController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/auth/steam', [SteamAuthController::class, 'login'])->name('auth.steam');
Route::get('/auth/steam/callback', [SteamAuthController::class, 'callback'])->name('auth.steam.callback');

require __DIR__.'/auth.php';
