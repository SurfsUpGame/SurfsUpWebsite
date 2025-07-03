<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SteamAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;

if (env('APP_ENV') === 'production') {
    URL::forceHttps(true);
}

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/auth/steam', [SteamAuthController::class, 'login'])->name('auth.steam');
Route::get('/auth/steam/callback', [SteamAuthController::class, 'callback'])->name('auth.steam.callback');

Route::get('/leaderboard/{steamId}', [LeaderboardController::class, 'show'])->name('leaderboard.show');

require __DIR__.'/auth.php';
