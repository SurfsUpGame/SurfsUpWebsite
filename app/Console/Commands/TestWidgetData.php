<?php

namespace App\Console\Commands;

use App\Models\PlayerCountHistory;
use App\Services\SteamPlayerCountService;
use Illuminate\Console\Command;

class TestWidgetData extends Command
{
    protected $signature = 'steam:test-widget';
    protected $description = 'Test widget data from database';

    public function handle()
    {
        $this->info('Testing widget data sources...');
        
        $steamService = new SteamPlayerCountService();
        $currentPlayers = $steamService->getCurrentPlayerCount();
        
        // Test database peak/low
        $peakAndLow = PlayerCountHistory::getPeakAndLowForLast24Hours();
        $dbHistory = PlayerCountHistory::getHistoryForLast24Hours();
        
        $this->info("📊 Widget Data Summary:");
        $this->line("Current Players: " . ($currentPlayers ?? 'N/A'));
        $this->line("24h Peak: " . ($peakAndLow['peak'] ?? 'N/A'));
        $this->line("24h Low: " . ($peakAndLow['low'] ?? 'N/A'));
        $this->line("History Records: " . $dbHistory->count());
        
        if ($peakAndLow['peak'] !== null) {
            $peakRecord = PlayerCountHistory::where('player_count', $peakAndLow['peak'])
                ->where('recorded_at', '>=', now()->subHours(24))
                ->orderBy('recorded_at', 'desc')
                ->first();
                
            if ($peakRecord) {
                $this->line("Peak Time: " . $peakRecord->recorded_at->format('Y-m-d H:i:s'));
            }
        }
        
        if ($peakAndLow['low'] !== null) {
            $lowRecord = PlayerCountHistory::where('player_count', $peakAndLow['low'])
                ->where('recorded_at', '>=', now()->subHours(24))
                ->orderBy('recorded_at', 'desc')
                ->first();
                
            if ($lowRecord) {
                $this->line("Low Time: " . $lowRecord->recorded_at->format('Y-m-d H:i:s'));
            }
        }
        
        $totalRecords = PlayerCountHistory::count();
        $this->line("Total Records: {$totalRecords}");
        
        return 0;
    }
}