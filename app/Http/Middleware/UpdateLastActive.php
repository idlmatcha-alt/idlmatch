<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateLastActive
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $request->user()->loadMissing('profile');
            $profile = $request->user()->profile;

            // Only update if last active was more than 2 minutes ago to reduce DB writes
            if ($profile && (!$profile->last_active_at || $profile->last_active_at->lt(now()->subMinutes(2)))) {
                $profile->update([
                    'last_active_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
