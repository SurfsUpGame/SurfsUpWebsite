<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MonitorTwitchStreams;

class MonitorTwitchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitch:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor Twitch streams and post new ones to Discord';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Twitch monitoring job...');
        
        MonitorTwitchStreams::dispatch();
        
        $this->info('Twitch monitoring job has been queued.');
        
        return 0;
    }
}