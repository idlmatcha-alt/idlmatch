<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Plan;
use App\Models\Profile;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Seo
{
    public static function site(string $key, $default = null)
    {
        return config("seo.site.$key", $default);
    }

    /**
     * Everything the head needs for the current request.
     */
    public static function forRequest(Request $request): array
    {
        $routeName = optional($request->route())->getName() ?? '';
        // Route names contain dots, which config() would read as nesting — index the array instead.
        $pages = (array) config('seo.pages', []);
        $dynamic = ['blog.show', 'success-stories.show', 'packages.select'];
        $known = isset($pages[$routeName]) || in_array($routeName, $dynamic, true);
        $page = $pages[$routeName] ?? [];

        $meta = [
            'route' => $routeName,
            'registered' => $known,
            'title' => $page['title'] ?? (self::site('name') . ' | Verified Rishta & Matchmaking'),
            'desc' => $page['desc'] ?? self::site('description'),
            'keys' => $page['keys'] ?? self::site('keywords'),
            // Anything not registered deliberately stays out of the index.
            'robots' => $page['robots'] ?? ($known ? config('seo.defaults.robots') : 'noindex,follow'),
            'type' => $page['type'] ?? 'website',
            'jsonld' => $page['jsonld'] ?? null,
            'image' => null,
            'image_alt' => null,
            'bare_title' => Str::before($page['title'] ?? '', ' | '),
        ];

        $meta['canonical'] = self::canonical($request, $routeName);
        $meta = self::applyModel($request, $routeName, $meta);

        $meta['image'] = self::absoluteUrl($meta['image'] ?? self::site('og_image'));
        $meta['image_alt'] = $meta['image_alt'] ?? self::site('name') . ' — ' . self::site('tagline');

        return $meta;
    }

    /**
     * Canonical URL. Faceted search collapses onto /search so filtered
     * copies never compete with each other; pagination keeps its page param.
     */
    public static function canonical(Request $request, string $routeName): string
    {
        $root = rtrim(self::baseUrl(), '/');

        if ($routeName === 'profiles.index') {
            return $root . '/search';
        }

        $path = '/' . ltrim($request->path(), '/');
        if ($path === '/' . ltrim($root, '/')) {
            $path = '/';
        }

        $page = $request->integer('page');

        return $root . ($path === '/' ? '/' : rtrim($path, '/')) . ($page > 1 ? '?page=' . $page : '');
    }

    public static function baseUrl(): string
    {
        return rtrim((string) config('app.url', ''), '/');
    }

    public static function absoluteUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (Str::startsWith($path, 'images/') || Str::startsWith($path, 'static/')) {
            return self::baseUrl() . '/' . ltrim($path, '/');
        }
        return self::baseUrl() . '/storage/' . ltrim($path, '/');
    }

    protected static function contentModel(Request $request, string $routeName)
    {
        if ($routeName === 'profiles.show') {
            return Profile::query()->where('display_id', $request->route('displayId'))->first();
        }
        return $request->route('blogPost') ?? $request->route('successStory') ?? $request->route('plan');
    }

    protected static function applyModel(Request $request, string $routeName, array $meta): array
    {
        $model = self::contentModel($request, $routeName);

        if ($model instanceof BlogPost) {
            $title = $model->meta_title ?: $model->title . ' | ' . self::site('short_name');
            $meta['title'] = Str::limit($title, 60, '');
            $meta['bare_title'] = $model->title;
            $meta['desc'] = $model->meta_description
                ?: Str::limit(trim($model->excerpt ?: strip_tags((string) $model->content)), 158, '...');
            $meta['keys'] = self::blogKeywords($model);
            $meta['type'] = 'article';
            $meta['jsonld'] = 'article';
            $meta['image'] = $model->featured_image ?: $model->cover_image;
            $meta['image_alt'] = $model->title . ' — ' . self::site('short_name') . ' marriage advice';
        }

        if ($model instanceof SuccessStory) {
            $names = $model->couple_names ?: trim($model->groom_name . ' & ' . $model->bride_name);
            $meta['title'] = Str::limit(($names ?: 'Real Couple') . ' | Marriage Success Story', 60, '');
            $meta['bare_title'] = $names ?: 'Success Story';
            $meta['desc'] = Str::limit(trim(strip_tags((string) $model->story)) ?: 'How this couple found each other through ' . self::site('short_name') . '.', 158, '...');
            $meta['keys'] = ['marriage success story', 'rishta success', 'real couples', self::site('short_name')];
            $meta['type'] = 'article';
            $meta['jsonld'] = 'article';
            $meta['image'] = $model->cover_image;
            $meta['image_alt'] = ($names ?: 'Couple') . ' — married through ' . self::site('short_name');
        }

        if ($model instanceof Plan) {
            $meta['title'] = Str::limit($model->name . ' Package | Rishta Plans & Price', 60, '');
            $meta['bare_title'] = $model->name;
            $meta['desc'] = Str::limit('What the ' . $model->name . ' rishta package includes: introductions, '
                . 'verification and consultant support for ' . $model->duration_days . ' days. Price in PKR on IDL Match.', 158, '...');
            $meta['keys'] = [$model->name . ' package', 'rishta package price', 'matrimony plan pkR'];
            $meta['jsonld'] = 'offer';
        }

        if ($model instanceof Profile) {
            $meta['title'] = Str::limit('Biodata ' . $model->display_id . ' | ' . ucfirst((string) $model->gender)
                . ($model->city ? ' from ' . $model->city : '') . ' | IDL Match', 60, '');
            $meta['bare_title'] = 'Profile ' . $model->display_id;
            $meta['desc'] = Str::limit('Verified matrimonial biodata ' . $model->display_id . ' — '
                . ($model->education ?: 'educated') . ', ' . ($model->profession ?: 'professional')
                . ', seeking marriage through ' . self::site('name') . '.', 158, '...');
            $meta['keys'] = ['biodata', 'rishta profile', 'verified bride', 'verified groom'];
            $meta['robots'] = config('seo.profile_page.robots');
        }

        return $meta;
    }

    protected static function blogKeywords(BlogPost $post): array
    {
        $stop = ['with', 'that', 'this', 'your', 'have', 'from', 'before', 'during', 'getting', 'they'];
        $words = preg_split('/[^a-z0-9]+/', strtolower((string) $post->title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $fromTitle = array_slice(array_values(array_filter($words, fn ($w) => strlen($w) > 3 && !in_array($w, $stop, true))), 0, 4);
        $base = ['marriage advice', 'rishta tips', 'matchmaking blog', 'nikah', 'matrimonial profile'];

        return array_values(array_unique(array_merge($fromTitle, $base)));
    }

    /**
     * JSON-LD graph for the current page.
     */
    public static function jsonLd(Request $request, array $meta): array
    {
        $graph = [];
        $root = rtrim(self::baseUrl(), '/');

        $graph[] = [
            '@type' => 'WebSite',
            '@id' => $root . '/#website',
            'url' => $root . '/',
            'name' => self::site('name'),
            'alternateName' => self::site('short_name'),
            'description' => self::site('description'),
            'inLanguage' => self::site('language'),
            'publisher' => ['@id' => $root . '/#organization'],
        ];

        $graph[] = self::organizationNode($root);

        if ($meta['jsonld'] === 'article') {
            $content = self::contentModel($request, $meta['route']);
            $graph[] = [
                '@type' => 'Article',
                '@id' => $meta['canonical'] . '#article',
                'headline' => Str::limit($meta['bare_title'], 110, ''),
                'name' => $meta['bare_title'],
                'description' => $meta['desc'],
                'datePublished' => (optional($content)->published_at ?? optional($content)->created_at)?->toAtomString(),
                'dateModified' => optional($content)->updated_at?->toAtomString(),
                'inLanguage' => self::site('language'),
                'isPartOf' => ['@id' => $root . '/#website'],
                'about' => ['@id' => $root . '/#organization'],
                'author' => ['@id' => $root . '/#organization'],
                'publisher' => ['@id' => $root . '/#organization'],
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $meta['canonical']],
                'image' => $meta['image'],
            ];
        }

        if ($meta['jsonld'] === 'webpage') {
            $graph[] = [
                '@type' => 'WebPage',
                '@id' => $meta['canonical'] . '#webpage',
                'url' => $meta['canonical'],
                'name' => $meta['bare_title'],
                'description' => $meta['desc'],
                'inLanguage' => self::site('language'),
                'isPartOf' => ['@id' => $root . '/#website'],
                'about' => ['@id' => $root . '/#organization'],
            ];
        }

        if ($meta['jsonld'] === 'faq') {
            $graph[] = [
                '@type' => 'FAQPage',
                '@id' => $meta['canonical'] . '#faq',
                'mainEntity' => array_map(fn ($f) => [
                    '@type' => 'Question',
                    'name' => $f['q'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                ], self::faqItems()),
            ];
        }

        if ($meta['jsonld'] === 'services') {
            $graph[] = [
                '@type' => 'Service',
                '@id' => $meta['canonical'] . '#service',
                'name' => $meta['bare_title'],
                'description' => $meta['desc'],
                'serviceType' => 'Matrimonial matchmaking service',
                'provider' => ['@id' => $root . '/#organization'],
                'areaServed' => array_map(fn ($c) => ['@type' => 'Country', 'name' => $c], config('seo.org.area_served')),
                'url' => $meta['canonical'],
            ];
        }

        if ($meta['jsonld'] === 'offer') {
            $plan = self::contentModel($request, $meta['route']);
            $graph[] = [
                '@type' => 'Offer',
                '@id' => $meta['canonical'] . '#offer',
                'name' => $meta['bare_title'],
                'description' => $meta['desc'],
                'url' => $meta['canonical'],
                'priceCurrency' => $plan->currency ?? 'PKR',
                'price' => (string) ($plan->price ?? 0),
                'availability' => 'https://schema.org/InStock',
                'seller' => ['@id' => $root . '/#organization'],
            ];
        }

        if ($meta['jsonld'] === 'search') {
            $graph[] = [
                '@type' => 'ItemList',
                '@id' => $meta['canonical'] . '#list',
                'name' => 'Verified rishta profiles',
                'itemListElement' => self::searchResultItems(),
            ];
        }

        $crumbs = self::breadcrumbs($request, $meta);
        if (count($crumbs) > 1) {
            $graph[] = [
                '@type' => 'BreadcrumbList',
                '@id' => $meta['canonical'] . '#breadcrumb',
                'itemListElement' => $crumbs,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter($graph, fn ($n) => !empty($n))),
        ];
    }

    public static function organizationNode(string $root): array
    {
        $org = config('seo.org');

        return [
            '@type' => 'Organization',
            '@id' => $root . '/#organization',
            'name' => self::site('name'),
            'alternateName' => self::site('short_name'),
            'legalName' => $org['legal_name'],
            'url' => $root . '/',
            'logo' => self::absoluteUrl($org['logo']),
            'email' => $org['email'],
            'foundingDate' => (string) $org['founding_year'],
            'contactPoint' => array_map(fn ($p) => [
                '@type' => 'ContactPoint',
                'telephone' => $p,
                'contactType' => 'customer service',
                'email' => $org['email'],
                'availableLanguage' => ['English', 'Urdu'],
            ], $org['phones']),
            'areaServed' => $org['area_served'],
            'sameAs' => array_values(array_filter([self::site('facebook'), self::site('twitter')])),
            'slogan' => self::site('tagline'),
            'knowsAbout' => self::site('keywords'),
        ];
    }

    public static function faqItems(): array
    {
        return config('faqs.items', []);
    }

    protected static function searchResultItems(): array
    {
        return Profile::query()
            ->visibleToUsers()
            ->latest()
            ->limit(12)
            ->get()
            ->values()
            ->map(fn (Profile $p, int $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'url' => self::baseUrl() . '/profiles/' . $p->display_id,
                'name' => 'Rishta profile ' . $p->display_id,
            ])->all();
    }

    protected static function breadcrumbs(Request $request, array $meta): array
    {
        $root = rtrim(self::baseUrl(), '/');
        $crumbs = [
            ['name' => 'Home', 'url' => $root . '/'],
        ];

        $sections = [
            'blog.index' => ['Blog', $root . '/blog'],
            'blog.show' => ['Blog', $root . '/blog'],
            'success-stories.index' => ['Success Stories', $root . '/success-stories'],
            'success-stories.show' => ['Success Stories', $root . '/success-stories'],
            'packages.index' => ['Packages', $root . '/packages'],
            'packages.select' => ['Packages', $root . '/packages'],
            'profiles.index' => ['Search Profiles', $root . '/search'],
            'profiles.show' => ['Search Profiles', $root . '/search'],
            'abroad.proposals' => ['Overseas Rishta', $root . '/abroad-proposals'],
            'elite.matrimonial' => ['Elite Matrimonial', $root . '/elite-matrimonial'],
            'about.our-services' => ['Services', $root . '/our-services'],
            'about' => ['About Us', $root . '/about'],
            'contact' => ['Contact', $root . '/contact'],
            'faqs' => ['FAQs', $root . '/faqs'],
        ];

        if (isset($sections[$meta['route']])) {
            $crumbs[] = ['name' => $sections[$meta['route']][0], 'url' => $sections[$meta['route']][1]];
        }

        if (in_array($meta['route'], ['blog.show', 'success-stories.show', 'packages.select', 'profiles.show'], true)) {
            $crumbs[] = ['name' => $meta['bare_title'], 'url' => $meta['canonical']];
        }

        return array_values(array_map(fn (array $c, int $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $c['name'],
            'item' => $c['url'],
        ], $crumbs, array_keys($crumbs)));
    }

    public static function sitemapEntries(): array
    {
        $urls = [];

        foreach ((array) config('seo.pages') as $name => $page) {
            if (($page['sitemap'] ?? true) === false) {
                continue;
            }
            if (Str::contains($page['robots'] ?? config('seo.defaults.robots'), 'noindex')) {
                continue;
            }
            try {
                $urls[] = [
                    'loc' => route($name),
                    'lastmod' => null,
                    'changefreq' => $page['freq'] ?? config('seo.defaults.changefreq'),
                    'priority' => $page['pri'] ?? config('seo.defaults.priority'),
                ];
            } catch (\Throwable) {
                // route name without a URL (named sub-route) — skip
            }
        }

        BlogPost::query()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (BlogPost $p) use (&$urls) {
                $urls[] = [
                    'loc' => route('blog.show', $p->slug),
                    'lastmod' => $p->updated_at?->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            });

        SuccessStory::query()
            ->where('status', 'approved')
            ->get(['id', 'updated_at'])
            ->each(function (SuccessStory $s) use (&$urls) {
                $urls[] = [
                    'loc' => route('success-stories.show', $s),
                    'lastmod' => $s->updated_at?->toAtomString(),
                    'changefreq' => 'yearly',
                    'priority' => '0.6',
                ];
            });

        Plan::query()->where('is_active', true)->get(['id', 'updated_at'])
            ->each(function (Plan $p) use (&$urls) {
                $urls[] = [
                    'loc' => route('packages.select', $p),
                    'lastmod' => $p->updated_at?->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            });

        return $urls;
    }
}
