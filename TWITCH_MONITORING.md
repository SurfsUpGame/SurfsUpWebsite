# Twitch Stream Monitoring System

This system automatically monitors Twitch streams for your game and posts new live streams to Discord via webhooks.

## Features

- ✅ Monitors Twitch streams every 5 minutes
- ✅ Posts new streams to Discord with rich embeds
- ✅ Prevents duplicate posts using cache system
- ✅ Automatically removes offline streams from cache
- ✅ Handles Twitch API authentication
- ✅ Comprehensive logging and error handling

## Setup

### 1. Twitch API Setup

1. Go to [Twitch Developer Console](https://dev.twitch.tv/console/apps)
2. Create a new application
3. Get your Client ID and Client Secret
4. Find your game's ID using the Twitch API:
   ```bash
   curl -X GET 'https://api.twitch.tv/helix/games?name=SurfsUp' \
   -H 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
   -H 'Client-Id: YOUR_CLIENT_ID'
   ```

### 2. Discord Webhook Setup

1. Go to your Discord server
2. Navigate to Channel Settings → Integrations → Webhooks
3. Create a new webhook
4. Copy the webhook URL

### 3. Environment Configuration

Add these variables to your `.env` file:

```env
# Twitch API Configuration
TWITCH_CLIENT_ID=your_twitch_client_id_here
TWITCH_CLIENT_SECRET=your_twitch_client_secret_here
TWITCH_GAME_ID=your_surfsup_game_id_here
TWITCH_GAME_TITLE=SurfsUp

# Discord Webhook URL
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/your_webhook_url_here
```

### 4. Queue Configuration

Make sure your Laravel queue is configured and running:

```bash
# Start the queue worker
php artisan queue:work

# Or use supervisor in production
```

### 5. Schedule Configuration

The job is automatically scheduled to run every 5 minutes. Make sure your Laravel scheduler is running:

```bash
# Add this to your crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Usage

### Manual Testing

Test the system manually:

```bash
# Test the monitoring system
php artisan twitch:test

# Clear the cache (to repost streams)
php artisan twitch:test --clear-cache

# Run monitoring manually
php artisan twitch:monitor
```

### Queue the Job Manually

```bash
# Dispatch the job to the queue
php artisan tinker
>>> App\Jobs\MonitorTwitchStreams::dispatch();
```

## How It Works

### 1. Authentication
- Gets Twitch access token using Client Credentials flow
- Caches token until 90% of expiry time
- Automatically refreshes when needed

### 2. Stream Monitoring
- Fetches live streams for your game from Twitch API
- Compares with cached list of already-posted streams
- Identifies new streams that haven't been posted

### 3. Discord Posting
- Creates rich embed with stream information
- Posts to Discord via webhook
- Includes stream thumbnail, viewer count, and direct link

### 4. Cache Management
- Caches posted streams by stream ID
- Removes offline streams from cache
- Prevents duplicate Discord posts

## Cache Structure

```php
// Cached streams format
[
    'stream_id_123' => [
        'id' => 'stream_id_123',
        'user_name' => 'StreamerName',
        'title' => 'Stream Title',
        'posted_at' => '2024-01-01T12:00:00Z'
    ]
]
```

## Discord Message Format

The bot posts messages with:
- 🔴 Live notification
- Streamer name and stream title
- Game name and viewer count
- Stream thumbnail
- Direct link to stream
- Twitch branding

## Troubleshooting

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Common Issues

1. **No streams found**: Check if `TWITCH_GAME_ID` is correct
2. **Discord posts not appearing**: Verify webhook URL and permissions
3. **Authentication errors**: Check Twitch credentials
4. **Job not running**: Ensure queue worker and scheduler are active

### Environment Variables Check
```bash
php artisan twitch:test
```

## Monitoring

The system logs all activities:
- Stream discovery
- Discord posts
- Cache operations
- API errors
- Authentication issues

Check Laravel logs for detailed information about system operation.

## Customization

### Change Monitoring Frequency

Edit `bootstrap/app.php`:
```php
$schedule->command('twitch:monitor')
         ->everyTenMinutes() // Change frequency here
         ->withoutOverlapping()
         ->runInBackground();
```

### Customize Discord Message

Edit the `postToDiscord()` method in `app/Jobs/MonitorTwitchStreams.php` to modify:
- Message content
- Embed design
- Additional fields
- Colors and formatting

### Add Stream Filters

Modify `fetchLiveStreams()` to add additional filtering:
- Minimum viewer count
- Stream language
- Stream tags
- User criteria