<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: [
            '192.168.1.1',
            '10.0.0.0/8',
            '172.17.0.0/16',
            '172.18.0.0/16',
            '172.19.0.0/16',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('twitch:monitor')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
