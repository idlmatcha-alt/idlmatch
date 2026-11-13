<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnknownLogin;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $currentIp = $request->ip();

        // Check for login from new location
        if ($user->last_login_ip && $user->last_login_ip !== $currentIp) {
            try {
                Mail::to($user->email)->send(new UnknownLogin($user, $currentIp));
            } catch (\Exception $e) {
                // Log error but continue login
                \Illuminate\Support\Facades\Log::error('Failed to send UnknownLogin email: ' . $e->getMessage());
            }
        }

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $currentIp,
        ]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
