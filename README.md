# SurfsUp Website

Official website for the SurfsUp game, featuring player profiles, leaderboards, and community integration.

## Features

- Steam authentication and player profiles
- Real-time leaderboards
- Twitch stream integration
- Discord webhook notifications
- Admin dashboard

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB or SQLite
- Steam API access
- Twitch API access (optional)
- Discord webhook (optional)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/SurfsUpWebsite.git
cd SurfsUpWebsite
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Configure API Keys

You'll need to obtain and configure several API keys:

#### Steam API Configuration

1. Get your Steam Web API key:
   - Visit https://steamcommunity.com/dev/apikey
   - Login with your Steam account
   - Enter your domain name
   - Copy the generated API key

2. For publisher features, get a publisher key:
   - Visit https://partner.steamgames.com/
   - Navigate to your app's Web API settings
   - Generate a publisher API key

3. Add to your `.env`:
   ```
   STEAM_APP_ID=your_game_app_id
   STEAM_AUTH_API_KEY=your_steam_api_key
   STEAM_PUBLISHER_API_KEY=your_publisher_key
   ```

#### Twitch Integration (Optional)

1. Create a Twitch application:
   - Go to https://dev.twitch.tv/console/apps
   - Click "Register Your Application"
   - Name: SurfsUp Website
   - OAuth Redirect URLs: `https://yourdomain.com/auth/twitch/callback`
   - Category: Website Integration

2. Add to your `.env`:
   ```
   TWITCH_CLIENT_ID=your_client_id
   TWITCH_CLIENT_SECRET=your_client_secret
   ```

3. Find your game's Twitch ID:
   - Use the Twitch API to search for your game
   - Or check existing streams of your game

#### Discord Notifications (Optional)

1. Create a Discord webhook:
   - Open your Discord server
   - Go to Server Settings → Integrations
   - Click "Webhooks" → "New Webhook"
   - Choose the channel for notifications
   - Copy the webhook URL

2. Add to your `.env`:
   ```
   DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/xxx/yyy
   ```

#### Admin Configuration

Set your Steam ID as admin:

1. Find your Steam ID at https://steamid.io/
2. Add to your `.env`:
   ```
   ADMIN_STEAM_ID=76561198000000000
   ```

### 5. Database setup

Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

### 6. Build assets

```bash
npm run build
```

For development with hot-reload:

```bash
npm run dev
```

### 7. Start the application

For local development:

```bash
php artisan serve
```

Visit http://localhost:8000

## Docker Setup (Alternative)

If you prefer using Docker with Laravel Sail:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run build
```

## Security

Please see [SECURITY.md](SECURITY.md) for security policies and best practices.

## Testing

Run the test suite:

```bash
php artisan test
```

## Deployment

For production deployment:

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Ensure proper file permissions
5. Set up HTTPS
6. Configure your web server (nginx/Apache)

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.