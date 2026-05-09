<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserPresence
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            \Illuminate\Support\Facades\Cache::put('user-is-online-' . auth()->id(), true, now()->addMinutes(2));
        }

        return $next($request);
    }
}
