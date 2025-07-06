<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorTwitchStreams implements ShouldQueue
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
            $accessToken = $this->getTwitchAccessToken();

            if (!$accessToken) {
                Log::error('Failed to get Twitch access token');
                return;
            }

            $liveStreams = $this->fetchLiveStreams($accessToken);
            $cachedStreams = $this->getCachedStreams();

            // Remove offline streams from cache
            $this->removeOfflineStreams($liveStreams, $cachedStreams);

            // Post new streams to Discord and cache them
            $this->processNewStreams($liveStreams, $cachedStreams);

        } catch (\Exception $e) {
            Log::error('Error in MonitorTwitchStreams job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get Twitch access token
     */
    private function getTwitchAccessToken(): ?string
    {
        $clientId = config('services.twitch.client_id');
        $clientSecret = config('services.twitch.client_secret');

        if (!$clientId || !$clientSecret) {
            Log::error('Twitch credentials not configured');
            return null;
        }

        $cacheKey = 'twitch_access_token';
        $cachedToken = Cache::get($cacheKey);

        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $response = Http::post('https://id.twitch.tv/oauth2/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;

                // Cache token for 90% of its expiry time
                Cache::put($cacheKey, $token, ($expiresIn * 0.9));

                return $token;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get Twitch access token', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Fetch live streams from Twitch API
     */
    private function fetchLiveStreams(string $accessToken): array
    {
        $clientId = config('services.twitch.client_id');
        $gameId = config('services.twitch.game_id');

        if (!$gameId) {
            Log::warning('TWITCH_GAME_ID not configured, searching all streams');
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $clientId,
            ])->get('https://api.twitch.tv/helix/streams', [
                'game_id' => $gameId,
                'first' => 100, // Max streams to check
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch Twitch streams', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Get cached streams
     */
    private function getCachedStreams(): array
    {
        return Cache::get('twitch_posted_streams', []);
    }

    /**
     * Remove offline streams from cache
     */
    private function removeOfflineStreams(array $liveStreams, array $cachedStreams): void
    {
        $liveStreamIds = array_column($liveStreams, 'id');
        $updatedCache = [];

        foreach ($cachedStreams as $streamId => $streamData) {
            if (in_array($streamId, $liveStreamIds)) {
                $updatedCache[$streamId] = $streamData;
            } else {
                Log::info('Removing offline stream from cache', ['stream_id' => $streamId]);
            }
        }

        Cache::put('twitch_posted_streams', $updatedCache, 86400); // Cache for 24 hours
    }

    /**
     * Process new streams - post to Discord and cache them
     */
    private function processNewStreams(array $liveStreams, array $cachedStreams): void
    {
        $discordWebhookUrl = config('services.discord.webhook_url');

        if (!$discordWebhookUrl) {
            Log::warning('Discord webhook URL not configured');
            return;
        }

        $updatedCache = $cachedStreams;

        foreach ($liveStreams as $stream) {
            $streamId = $stream['id'];

            // Skip if already posted
            if (isset($cachedStreams[$streamId])) {
                Log::info('Stream already posted', ['stream_id' => $streamId]);
                continue;
            }

            Log::info('New stream detected', $stream);

            // Cache the stream
            $updatedCache[$streamId] = [
                'id' => $streamId,
                'user_name' => $stream['user_name'],
                'user_login' => $stream['user_login'],
                'title' => $stream['title'],
                'thumbnail_url' => $stream['thumbnail_url'],
                'posted_at' => now()->toISOString(),
            ];

            // Post to Discord
            if ($this->postToDiscord($stream, $discordWebhookUrl)) {
                Log::info('Posted new stream to Discord', [
                    'stream_id' => $streamId,
                    'user_name' => $stream['user_name']
                ]);
            }
        }

        Cache::put('twitch_posted_streams', $updatedCache, 86400); // Cache for 24 hours
    }

    /**
     * Post stream notification to Discord
     */
    private function postToDiscord(array $stream, string $webhookUrl): bool
    {
        $gameTitle = config('services.twitch.game_title');

        $embed = [
            'title' => "{$stream['user_name']} is now live!",
            'description' => $stream['title'],
            'url' => "https://twitch.tv/{$stream['user_login']}",
            'color' => 6570404, // Twitch purple color
            'image' => [
                'url' => str_replace(['{width}', '{height}'], ['1920', '1080'], $stream['thumbnail_url'])
            ],
            'fields' => [
                [
                    'name' => 'Viewers',
                    'value' => number_format($stream['viewer_count']),
                    'inline' => true
                ],
                [
                    'name' => 'Stream Language',
                    'value' => strtoupper($stream['language'] ?? 'EN'),
                    'inline' => true
                ],
                [
                    'name' => 'Watch Live',
                    'value' => "[Click here to watch](https://twitch.tv/{$stream['user_login']})",
                    'inline' => false
                ],
                [
                    'name' => 'Stream Info',
                    'value' => "Started: <t:" . strtotime($stream['started_at']) . ":R>",
                    'inline' => false
                ]
            ],
            'footer' => [
                'text' => 'Twitch • Live Stream Notification',
                'icon_url' => 'https://dev.twitch.tv/docs/assets/favicon-32x32.png'
            ],
            'timestamp' => now()->toISOString()
        ];

        $payload = [
            'content' => "**{$stream['user_name']}** is streaming **{$gameTitle}**!",
            'embeds' => [$embed]
        ];

        try {
            $response = Http::post($webhookUrl, $payload);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Failed to post to Discord', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception posting to Discord', ['error' => $e->getMessage()]);
        }

        return false;
    }
}
