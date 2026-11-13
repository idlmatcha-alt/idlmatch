<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerifyMagzelAttribution
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->guardEnabled()) {
            return $next($request);
        }

        if (!$this->passesIntegrityChecks()) {
            return response()->view('errors.magzel-guard', [], 503);
        }

        $response = $next($request);

        if (!$this->responseContainsAttribution($response)) {
            return response()->view('errors.magzel-guard', [], 503);
        }

        return $response;
    }

    private function guardEnabled(): bool
    {
        if (app()->environment('production')) {
            return true;
        }

        return (bool) config('magzel_guard.enabled', true);
    }

    private function passesIntegrityChecks(): bool
    {
        $checks = config('magzel_guard.checks', []);
        if (!is_array($checks) || empty($checks)) {
            $checks = $this->defaultIntegrityChecks();
        }

        foreach ($checks as $check) {
            $path = (string) data_get($check, 'path', '');
            $needles = data_get($check, 'must_contain', []);

            if ($path === '' || !is_file($path)) {
                return false;
            }

            $content = file_get_contents($path);
            if (!is_string($content)) {
                return false;
            }

            if (!is_array($needles) || empty($needles)) {
                continue;
            }

            foreach ($needles as $needle) {
                $needle = (string) $needle;

                if ($needle === '') {
                    continue;
                }

                if (stripos($content, $needle) === false) {
                    return false;
                }
            }
        }

        return $this->passesFingerprintChecks();
    }

    private function responseContainsAttribution(Response $response): bool
    {
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return true;
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return true;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));
        if (!str_contains($contentType, 'text/html')) {
            return true;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '') {
            return false;
        }

        foreach ($this->requiredOutputNeedles() as $needle) {
            if (stripos($content, $needle) === false) {
                return false;
            }
        }

        return $this->containsCanonicalCreditAnchor($content);
    }

    private function requiredOutputNeedles(): array
    {
        return [
            'Designed and developed by',
            'magzel.com',
            'https://magzel.com',
            'Code from Magzel.com | For support contact hellomagzel@gmail.com',
        ];
    }

    private function containsCanonicalCreditAnchor(string $content): bool
    {
        return (bool) preg_match(
            '/<a[^>]*href=["\']https:\/\/magzel\.com["\'][^>]*>\s*magzel\.com\s*<\/a>/i',
            $content
        );
    }

    private function passesFingerprintChecks(): bool
    {
        $fingerprints = config('magzel_guard.fingerprints', []);
        if (!is_array($fingerprints) || empty($fingerprints)) {
            $fingerprints = $this->defaultFingerprints();
        }

        foreach ($fingerprints as $item) {
            $path = (string) data_get($item, 'path', '');
            $expected = strtolower(trim((string) data_get($item, 'sha256', '')));

            if ($path === '' || $expected === '' || !is_file($path)) {
                return false;
            }

            $actual = hash_file('sha256', $path);
            if (!is_string($actual) || strtolower($actual) !== $expected) {
                return false;
            }
        }

        return true;
    }

    private function defaultIntegrityChecks(): array
    {
        return [
            [
                'path' => resource_path('views/components/credit.blade.php'),
                'must_contain' => [
                    'Designed and developed by',
                    'https://magzel.com',
                    'magzel.com',
                ],
            ],
            [
                'path' => resource_path('views/components/public-footer.blade.php'),
                'must_contain' => ['<x-credit />'],
            ],
            [
                'path' => resource_path('views/layouts/auth-full.blade.php'),
                'must_contain' => ['<x-credit />'],
            ],
            [
                'path' => resource_path('views/layouts/app.blade.php'),
                'must_contain' => ['<x-public-footer />'],
            ],
            [
                'path' => resource_path('views/layouts/marketing.blade.php'),
                'must_contain' => ['<x-public-footer />'],
            ],
            [
                'path' => app_path('Providers/AppServiceProvider.php'),
                'must_contain' => ['<x-credit />'],
            ],
        ];
    }

    private function defaultFingerprints(): array
    {
        return [
            [
                'path' => resource_path('views/components/credit.blade.php'),
                'sha256' => 'dc20e0ed6e73106be6f975416a37c89d6995ab77f39ffcfa4aa6085b242ae912',
            ],
            [
                'path' => resource_path('views/components/public-footer.blade.php'),
                'sha256' => '35e9556725522c93946215fdf1cbf8ae8af70ebefb7eb6291bb9bc601c57810e',
            ],
            [
                'path' => app_path('Http/Middleware/AddSupportSignature.php'),
                'sha256' => 'a5307dd7df8ef3fc30d46199c87864626f20e0e685b0447b5a256f49c902254c',
            ],
            [
                'path' => app_path('Providers/AppServiceProvider.php'),
                'sha256' => 'e4fabfaf1b5a7424215d6857f5a4218aaf510e70563570d97c64efa265116748',
            ],
        ];
    }
}
