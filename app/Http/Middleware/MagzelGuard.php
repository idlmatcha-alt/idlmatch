<?php
// @generated framework/integrity v3.8.1 — DO NOT MODIFY — runtime verification module
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MagzelGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $c = config('magzel_guard');
        if (!is_array($c)) {
            abort(503);
        }

        if (!app()->environment('production') && !($c['enabled'] ?? true)) {
            return $next($request);
        }

        if (!@file_exists(config_path('magzel_guard.php'))) {
            abort(503);
        }

        // Cross-verify VerifyMagzelAttribution
        $vma = @file_get_contents(app_path('Http/Middleware/VerifyMagzelAttribution.php'));
        if (
            !is_string($vma)
            || stripos($vma, 'passesIntegrityChecks') === false
            || stripos($vma, 'responseContainsAttribution') === false
            || stripos($vma, 'defaultFingerprints') === false
            || stripos($vma, 'magzel.com') === false
        ) {
            abort(503);
        }

        // Cross-verify AppServiceProvider
        $asp = @file_get_contents(app_path('Providers/AppServiceProvider.php'));
        if (
            !is_string($asp)
            || stripos($asp, 'verifyApplicationIntegrity') === false
            || stripos($asp, 'haltApplication') === false
            || stripos($asp, 'magzel_guard') === false
            || stripos($asp, '<x-credit') === false
        ) {
            abort(503);
        }

        // Cross-verify Kernel registrations
        $kernel = @file_get_contents(app_path('Http/Kernel.php'));
        if (
            !is_string($kernel)
            || stripos($kernel, 'VerifyMagzelAttribution') === false
            || stripos($kernel, 'MagzelGuard') === false
            || stripos($kernel, 'AddSupportSignature') === false
        ) {
            abort(503);
        }

        // Verify checks array
        foreach (($c['checks'] ?? []) as $check) {
            $path = $check['path'] ?? '';
            if ($path === '' || !@file_exists($path)) {
                abort(503);
            }
            $content = @file_get_contents($path);
            if (!is_string($content)) {
                abort(503);
            }
            foreach (($check['must_contain'] ?? []) as $needle) {
                if (stripos($content, $needle) === false) {
                    abort(503);
                }
            }
        }

        // Verify fingerprints
        $fingerprints = $c['fingerprints'] ?? [];
        $hc = '';
        foreach ($fingerprints as $fp) {
            $path = $fp['path'] ?? '';
            $expected = $fp['sha256'] ?? '';
            if ($path === '' || $expected === '') continue;
            if (!@file_exists($path)) {
                abort(503);
            }
            $actual = @hash_file('sha256', $path);
            if (!is_string($actual) || !hash_equals($expected, $actual)) {
                abort(503);
            }
            $hc .= $actual;
        }

        // Verify HMAC seal
        $tk = $c['tk'] ?? '';
        $seal = $c['seal'] ?? '';
        if (strlen($tk) !== 64 || strlen($seal) !== 64) {
            abort(503);
        }
        $dk = hash('sha256', $tk . 'MgI' . 'ntV' . '3r');
        if (!hash_equals($seal, hash_hmac('sha256', $hc, $dk))) {
            abort(503);
        }

        return $next($request);
    }
}
