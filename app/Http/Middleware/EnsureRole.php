<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles)) {
            throw new ApiException(
                message: 'You do not have permission to access this resource.',
                loc: ['permission'],
                type: 'authorization_error',
                status: 403
            );
        }

        return $next($request);
    }
}