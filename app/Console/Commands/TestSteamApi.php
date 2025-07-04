<?php

namespace App\Console\Commands;

use App\Services\SteamPlayerCountService;
use Illuminate\Console\Command;

class TestSteamApi extends Command
{
    protected $signature = 'steam:test-api';
    protected $description = 'Test Steam API connection and player count';

    public function handle()
    {
        $this->info('Testing Steam API...');
        
        $service = new SteamPlayerCountService();
        $playerCount = $service->getCurrentPlayerCount();
        
        if ($playerCount !== null) {
            $this->info("✅ Steam API working! Current players: {$playerCount}");
            
            $playerData = $service->getPlayerCountWithHistory();
            $this->info("📊 Player data:");
            $this->line("Current: " . ($playerData['current'] ?? 'N/A'));
            $this->line("24h Peak: " . ($playerData['peak_24h'] ?? 'N/A'));
            $this->line("24h Low: " . ($playerData['low_24h'] ?? 'N/A'));
            $this->line("History entries: " . count($playerData['history']));
        } else {
            $this->error("❌ Steam API failed or returned null");
        }
        
        return 0;
    }
}