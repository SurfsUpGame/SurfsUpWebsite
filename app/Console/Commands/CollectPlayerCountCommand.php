<?php

namespace App\Console\Commands;

use App\Services\SteamPlayerCountService;
use App\Models\PlayerCountHistory;
use Illuminate\Console\Command;

class CollectPlayerCountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:collect-player-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect current player count from Steam API and store in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Collecting Steam player count...');
        
        $steamService = new SteamPlayerCountService();
        $playerCount = $steamService->getCurrentPlayerCountAndRecord();
        
        if ($playerCount !== null) {
            $this->info("✅ Successfully recorded {$playerCount} players at " . now()->format('Y-m-d H:i:s'));
            
            // Show some statistics
            $totalRecords = PlayerCountHistory::count();
            $this->line("📊 Total records in database: {$totalRecords}");
            
            $peakAndLow = PlayerCountHistory::getPeakAndLowForLast24Hours();
            if ($peakAndLow['peak'] !== null) {
                $this->line("🔝 24h Peak: {$peakAndLow['peak']} players");
                $this->line("📉 24h Low: {$peakAndLow['low']} players");
            }
        } else {
            $this->error("❌ Failed to fetch player count from Steam API");
            return 1;
        }
        
        return 0;
    }
}
