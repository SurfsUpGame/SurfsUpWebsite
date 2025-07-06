<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Jobs
|--------------------------------------------------------------------------
|
| Here is where you can define all of the scheduled jobs for your
| application. These jobs will be run by the Laravel scheduler.
|
*/

Schedule::job(new \App\Jobs\MonitorTwitchStreams())
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new \App\Jobs\CacheLeaderboards())
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new \App\Jobs\CollectSteamPlayerCount())
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new \App\Jobs\CleanupPlayerHistory())
    ->monthly()
    ->withoutOverlapping()
    ->onOneServer();

