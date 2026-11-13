<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AddSupportSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '') {
            return $response;
        }

        $creditAnchorPattern = '/<a[^>]*href=["\']https:\/\/magzel\.com["\'][^>]*>\s*magzel\.com\s*<\/a>/i';
        $creditAnchor = '<span data-magzel-credit style="display:none !important;">Designed and developed by <a href="https://magzel.com" rel="noopener noreferrer">magzel.com</a></span>';
        $signature = '<!-- Code from Magzel.com | For support contact hellomagzel@gmail.com -->';
        $shouldInjectAnchor = preg_match($creditAnchorPattern, $content) !== 1;
        $shouldInjectSignature = !str_contains($content, $signature);

        if (!$shouldInjectAnchor && !$shouldInjectSignature) {
            return $response;
        }

        $injections = [];
        if ($shouldInjectAnchor) {
            $injections[] = $creditAnchor;
        }
        if ($shouldInjectSignature) {
            $injections[] = $signature;
        }

        $injectText = implode(PHP_EOL, $injections);
        if (stripos($content, '</body>') !== false) {
            $content = preg_replace('/<\/body>/i', $injectText . PHP_EOL . '</body>', $content, 1) ?? $content;
        } else {
            $content .= PHP_EOL . $injectText;
        }

        $response->setContent($content);
        $response->headers->set('Content-Length', (string) strlen($content));

        return $response;
    }
}
