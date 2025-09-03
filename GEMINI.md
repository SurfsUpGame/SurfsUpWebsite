# SurfsUp Website

## Project Overview

This is the official website for the game "SurfsUp". It is a [Laravel](https://laravel.com/) (PHP) application with a [Vue.js](https://vuejs.org/) frontend. The project provides player profiles, leaderboards, and community integration features.

Key technologies used:
- **Backend**: Laravel (PHP)
- **Frontend**: Vue.js, Tailwind CSS, Vite
- **Database**: MySQL/MariaDB or SQLite
- **Authentication**: Steam OpenID
- **Admin Panel**: Filament
- **Testing**: Pest (PHP)

## Building and Running

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB or SQLite

### Installation & Setup

1.  **Install Dependencies**:
    ```bash
    composer install
    npm install
    ```

2.  **Environment Configuration**:
    - Copy `.env.example` to `.env`: `cp .env.example .env`
    - Generate an application key: `php artisan key:generate`
    - Configure your database and API keys (Steam, Twitch, Discord) in the `.env` file.

3.  **Database Migration**:
    ```bash
    php artisan migrate --seed
    ```

### Development

-   **Run development servers**:
    ```bash
    npm run dev
    ```
    This will start the Vite development server for the frontend and the Laravel development server.

-   **Run tests**:
    ```bash
    php artisan test
    ```

### Building for Production

-   **Build frontend assets**:
    ```bash
    npm run build
    ```

-   **Laravel optimizations**:
    ```bash
    php artisan config:cache
    php artisan route:cache
    ```

## Development Conventions

-   **Routing**: Web routes are defined in `routes/web.php` and API routes in `routes/api.php`.
-   **Frontend Components**: Vue components are located in `resources/js/components`.
-   **Styling**: [Tailwind CSS](https://tailwindcss.com/) is used for styling. Configuration is in `tailwind.config.js`.
-   **Admin Panel**: The admin panel is built with [Filament](https://filamentphp.com/). Admin resources are located in `app/Filament/Admin`.
-   **Reactive Components**: The project uses [Livewire Volt](https://livewire.laravel.com/docs/volt) for single-file reactive components.
-   **Testing**: [Pest](https://pestphp.com/) is the preferred testing framework. Tests are in the `tests` directory.
