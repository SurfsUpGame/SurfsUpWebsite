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
Route::post('/roadmap/task/{task}/vote', [RoadmapController::class, 'vote'])->name('roadmap.task.vote')->middleware('auth');
Route::post('/roadmap/sprint/{sprint}/end', [RoadmapController::class, 'endSprint'])->name('roadmap.sprint.end')->middleware('auth');

// Suggestion Routes
Route::post('/roadmap/suggestions', [RoadmapController::class, 'storeSuggestion'])->name('roadmap.suggestions.store')->middleware('auth');
Route::post('/roadmap/suggestions/{suggestion}/vote', [RoadmapController::class, 'voteSuggestion'])->name('roadmap.suggestions.vote')->middleware('auth');
Route::post('/roadmap/suggestions/{suggestion}/convert', [RoadmapController::class, 'convertSuggestionToTask'])->name('roadmap.suggestions.convert')->middleware('auth');

// Impersonation Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/impersonate', [App\Http\Controllers\ImpersonationController::class, 'userList'])->name('admin.impersonate.list');
    Route::post('/admin/impersonate/{user}', [App\Http\Controllers\ImpersonationController::class, 'start'])->name('admin.impersonate.start');
    Route::get('/admin/impersonate/stop', [App\Http\Controllers\ImpersonationController::class, 'stop'])->name('admin.impersonate.stop');
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/steam-news', [App\Http\Controllers\SteamNewsController::class, 'index'])->name('api.steam-news');
});

require __DIR__.'/auth.php';
