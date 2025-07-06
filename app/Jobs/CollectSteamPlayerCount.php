<?php

namespace App\Jobs;

use App\Services\SteamPlayerCountService;
use App\Models\PlayerCountHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CollectSteamPlayerCount implements ShouldQueue
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
            $steamService = new SteamPlayerCountService();
            $playerCount = $steamService->getCurrentPlayerCountAndRecord();
            
            if ($playerCount !== null) {
                Log::info("Successfully recorded {$playerCount} players at " . now()->format('Y-m-d H:i:s'));
                
                // Log some statistics
                $totalRecords = PlayerCountHistory::count();
                Log::info("Total player count records in database: {$totalRecords}");
                
                $peakAndLow = PlayerCountHistory::getPeakAndLowForLast24Hours();
                if ($peakAndLow['peak'] !== null) {
                    Log::info("24h Peak: {$peakAndLow['peak']} players, 24h Low: {$peakAndLow['low']} players");
                }
            } else {
                Log::error("Failed to fetch player count from Steam API");
            }
        } catch (\Exception $e) {
            Log::error('Error in CollectSteamPlayerCount job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to mark job as failed
            throw $e;
        }
    }
}