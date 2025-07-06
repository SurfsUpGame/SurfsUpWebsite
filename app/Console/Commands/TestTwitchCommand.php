<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MonitorTwitchStreams;
use Illuminate\Support\Facades\Cache;

class TestTwitchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitch:test {--clear-cache : Clear the cached streams}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Twitch monitoring system and optionally clear cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear-cache')) {
            Cache::forget('twitch_posted_streams');
            Cache::forget('twitch_access_token');
            $this->info('✅ Cleared Twitch cache (posted streams and access token)');
            return 0;
        }

        $this->info('🔍 Testing Twitch monitoring system...');
        
        // Check environment variables
        $this->checkEnvironmentVariables();
        
        // Show cached streams
        $this->showCachedStreams();
        
        // Run the job synchronously for testing
        $this->info('🚀 Running Twitch monitoring job...');
        
        try {
            $job = new MonitorTwitchStreams();
            $job->handle();
            $this->info('✅ Job completed successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Job failed: ' . $e->getMessage());
            $this->line('Check the logs for more details.');
        }
        
        return 0;
    }

    private function checkEnvironmentVariables()
    {
        $this->info('🔧 Checking configuration variables...');
        
        $requiredVars = [
            'TWITCH_CLIENT_ID' => config('services.twitch.client_id'),
            'TWITCH_CLIENT_SECRET' => config('services.twitch.client_secret'),
            'TWITCH_GAME_ID' => config('services.twitch.game_id'),
            'DISCORD_WEBHOOK_URL' => config('services.discord.webhook_url'),
        ];
        
        foreach ($requiredVars as $var => $value) {
            if ($value) {
                $this->line("✅ {$var}: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value));
            } else {
                $this->line("❌ {$var}: Not set");
            }
        }
        
        $this->newLine();
    }

    private function showCachedStreams()
    {
        $cachedStreams = Cache::get('twitch_posted_streams', []);
        
        if (empty($cachedStreams)) {
            $this->info('📋 No cached streams found');
        } else {
            $this->info('📋 Cached streams (' . count($cachedStreams) . '):');
            foreach ($cachedStreams as $streamId => $streamData) {
                $this->line("  - {$streamData['user_name']}: {$streamData['title']} (Posted: {$streamData['posted_at']})");
            }
        }
        
        $this->newLine();
    }
}