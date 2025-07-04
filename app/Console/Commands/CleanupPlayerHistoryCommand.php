<?php

namespace App\Console\Commands;

use App\Models\PlayerCountHistory;
use Illuminate\Console\Command;

class CleanupPlayerHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:cleanup-history {--days=30 : Number of days to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old player count history records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = (int) $this->option('days');
        
        $this->info("Cleaning up player count history older than {$daysToKeep} days...");
        
        $deletedCount = PlayerCountHistory::cleanupOldRecords($daysToKeep);
        
        if ($deletedCount > 0) {
            $this->info("✅ Successfully deleted {$deletedCount} old records");
        } else {
            $this->info("ℹ️  No old records found to delete");
        }
        
        $remainingCount = PlayerCountHistory::count();
        $this->line("📊 Total records remaining: {$remainingCount}");
        
        return 0;
    }
}