<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserScoreController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| User Score API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('user')->group(function () {
    Route::get('{steamId}/score', [UserScoreController::class, 'getUserScore'])
        ->name('api.user.score');
    
    Route::get('{steamId}/scores', [UserScoreController::class, 'getAllUserScores'])
        ->name('api.user.scores');
    
    Route::get('{steamId}/leaderboard/{leaderboardId}', [UserScoreController::class, 'getUserLeaderboardRank'])
        ->name('api.user.leaderboard.rank');
});