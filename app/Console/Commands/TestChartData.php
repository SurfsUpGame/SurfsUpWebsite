<?php

namespace App\Console\Commands;

use App\Services\SteamPlayerCountService;
use Illuminate\Console\Command;

class TestChartData extends Command
{
    protected $signature = 'steam:test-chart';
    protected $description = 'Test chart data generation';

    public function handle()
    {
        $this->info('Testing chart data generation...');
        
        $service = new SteamPlayerCountService();
        $chartData = $service->getChartData();
        
        $this->info("📊 Chart data points: " . count($chartData));
        
        if (!empty($chartData)) {
            $this->info("Sample data points:");
            $sample = array_slice($chartData, -5); // Last 5 entries
            
            foreach ($sample as $entry) {
                $time = isset($entry['label']) ? $entry['label'] : 
                    \Carbon\Carbon::parse($entry['timestamp'])->format('H:i');
                $this->line("  {$time}: {$entry['count']} players");
            }
        }
        
        return 0;
    }
}