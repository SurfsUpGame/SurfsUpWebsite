<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SteamAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\RoadmapController;

if (env('APP_ENV') === 'production') {
    URL::forceHttps(true);
}

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/auth/steam', [SteamAuthController::class, 'login'])->name('auth.steam');
Route::get('/auth/steam/callback', [SteamAuthController::class, 'callback'])->name('auth.steam.callback');

Route::get('/leaderboard/{steamId}', [LeaderboardController::class, 'show'])->name('leaderboard.show');

Route::get('/roadmap', [RoadmapController::class, 'index'])->name('roadmap');
Route::post('/roadmap', [RoadmapController::class, 'store'])->name('roadmap.store')->middleware('auth');
Route::patch('/roadmap/task/{task}/status', [RoadmapController::class, 'updateStatus'])->name('roadmap.task.update-status')->middleware('auth');
Route::patch('/roadmap/task/{task}', [RoadmapController::class, 'update'])->name('roadmap.task.update')->middleware('auth');
Route::patch('/roadmap/task/{task}/archive', [RoadmapController::class, 'archive'])->name('roadmap.task.archive')->middleware('auth');
Route::delete('/roadmap/task/{task}', [RoadmapController::class, 'destroy'])->name('roadmap.task.destroy')->middleware('auth');

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/steam-news', [App\Http\Controllers\SteamNewsController::class, 'index'])->name('api.steam-news');
});

require __DIR__.'/auth.php';
