<?php

namespace App\Jobs;

use App\Models\PlayerCountHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupPlayerHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of days to keep
     *
     * @var int
     */
    protected $daysToKeep;

    /**
     * Create a new job instance.
     */
    public function __construct(int $daysToKeep = 30)
    {
        $this->daysToKeep = $daysToKeep;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Cleaning up player count history older than {$this->daysToKeep} days...");
            
            $deletedCount = PlayerCountHistory::cleanupOldRecords($this->daysToKeep);
            
            if ($deletedCount > 0) {
                Log::info("Successfully deleted {$deletedCount} old player count records");
            } else {
                Log::info("No old player count records found to delete");
            }
            
            $remainingCount = PlayerCountHistory::count();
            Log::info("Total player count records remaining: {$remainingCount}");
            
        } catch (\Exception $e) {
            Log::error('Error in CleanupPlayerHistory job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to mark job as failed
            throw $e;
        }
    }
}