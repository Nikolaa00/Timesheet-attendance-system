<?php

namespace App\Http\Middleware;

use App\Exceptions\UserNotActiveException;
use Closure;
use Illuminate\Http\Request;

class IsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && !$user->is_active) {
            throw new UserNotActiveException();
        }

        return $next($request);
    }
}
