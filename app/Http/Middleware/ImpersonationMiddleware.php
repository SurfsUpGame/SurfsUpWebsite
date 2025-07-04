<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add impersonation status to the view data
        if (Session::has('impersonator_id')) {
            view()->share('impersonating', true);
            view()->share('impersonator_id', Session::get('impersonator_id'));
        } else {
            view()->share('impersonating', false);
        }

        return $next($request);
    }
}