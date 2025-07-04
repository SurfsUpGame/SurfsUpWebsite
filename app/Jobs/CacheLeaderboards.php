<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\SteamLeaderboardService;

class CacheLeaderboards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $leaderboardService = new SteamLeaderboardService();
            $leaderboards = $leaderboardService->getLeaderboards();
            
            Log::info('Leaderboards cache updated', [
                'count' => count($leaderboards),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error caching leaderboards', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}