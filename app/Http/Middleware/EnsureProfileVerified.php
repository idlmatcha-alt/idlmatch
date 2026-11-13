<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Use loadMissing to avoid re-querying if already loaded by UpdateLastActive
        $user->loadMissing('profile');

        if (!$user->profile) {
            return $next($request);
        }

        return $next($request);
    }
}
