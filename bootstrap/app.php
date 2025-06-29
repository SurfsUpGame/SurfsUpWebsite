<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustHosts(at: ['surfsup.website', 'live.arneman.me']);
        $middleware->trustProxies(at: ['127.0.0.1', '192.168.0.1', '10.0.1.117/24', '172.19.0.0/16']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
