<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UseSanctumTokenFromCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken() && $request->hasCookie('session_id')) {
            $request->headers->set(
                'Authorization',
                'Bearer ' . $request->cookie('session_id')
            );
        }

        return $next($request);
    }
}
