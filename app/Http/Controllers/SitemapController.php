<?php

namespace App\Http\Controllers;

use App\Support\Seo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('seo.sitemap.xml', now()->addHour(), fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8'])
            ->header('X-Robots-Tag', 'noindex');
    }

    protected function build(): string
    {
        $entries = collect(Seo::sitemapEntries())
            ->map(fn (array $e) => [...$e, 'loc' => Seo::absoluteUrl($e['loc'])])
            ->unique('loc')
            ->sortBy(fn ($e) => [- (float) $e['priority'], $e['loc']])
            ->values();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($entries as $e) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($e['loc'], ENT_QUOTES, 'UTF-8') . "</loc>\n";
            if (!empty($e['lastmod'])) {
                $xml .= '    <lastmod>' . $e['lastmod'] . "</lastmod>\n";
            }
            $xml .= '    <changefreq>' . $e['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . number_format((float) $e['priority'], 1) . "</priority>\n";
            $xml .= "  </url>\n";
        }

        return $xml . '</urlset>';
    }
}
