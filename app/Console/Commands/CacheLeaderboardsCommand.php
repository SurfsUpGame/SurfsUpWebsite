<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CacheLeaderboards;

class CacheLeaderboardsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache Steam leaderboards data to improve performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching leaderboards caching job...');
        
        CacheLeaderboards::dispatch();
        
        $this->info('Leaderboards caching job has been queued.');
        
        return 0;
    }
}