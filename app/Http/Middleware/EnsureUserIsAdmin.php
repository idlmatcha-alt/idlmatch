<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards every page inside the Filament panel.
 *
 * Filament v2 only asks "is this visitor signed in?" — and because the panel
 * shares the `web` guard with the member site, any member who typed
 * /admin/login with their own account would have landed on the dashboard and
 * been able to browse other members' profiles, payments and messages. This
 * middleware is registered in config/filament.php under
 * `middleware.auth`, so it runs for every panel route after the session and
 * authentication middleware, but never for the login/logout forms.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        if (! $user) {
            return redirect()->route('filament.auth.login');
        }

        // A signed-in member is not staff. Send them back to their own area
        // instead of logging them out, so browsing the site is not disrupted.
        return redirect()
            ->route('dashboard')
            ->with('status', 'That account is not a staff account, so the admin panel is closed to it.');
    }
}
