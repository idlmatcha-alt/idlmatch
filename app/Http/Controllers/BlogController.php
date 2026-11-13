<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $seed = trim((string) request('seed', now()->format('YmdH')));
        if ($seed === '') {
            $seed = now()->format('YmdH');
        }

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $uniquePosts = $this->publishedPostsQuery()
            ->inRandomOrder($seed)
            ->take(200)
            ->get()
            ->unique(fn (BlogPost $post) => Str::lower(trim((string) $post->title)))
            ->values();

        $posts = new LengthAwarePaginator(
            $uniquePosts->forPage($page, $perPage)->values(),
            $uniquePosts->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        $posts->appends(['seed' => $seed]);

        $titleText = $posts->getCollection()
            ->pluck('title')
            ->filter()
            ->implode(' ');
        $countryHint = $this->detectCountryFromText($titleText);
        $sidebarProfiles = $this->buildSidebarProfiles($countryHint, 6);

        return view('blog.index', compact('posts', 'sidebarProfiles', 'countryHint'));
    }

    public function show(BlogPost $blogPost)
    {
        if (
            $blogPost->status !== 'published'
            || ($blogPost->published_at && $blogPost->published_at->isFuture())
        ) {
            abort(404);
        }

        $blogText = collect([
            $blogPost->title,
            $blogPost->excerpt,
            strip_tags((string) $blogPost->content),
        ])->filter()->implode(' ');

        $countryHint = $this->detectCountryFromText($blogText);
        $sidebarProfiles = $this->buildSidebarProfiles($countryHint, 6);
        $latestPosts = $this->publishedPostsQuery()
            ->where('id', '!=', $blogPost->id)
            ->inRandomOrder(now()->format('YmdH'))
            ->take(60)
            ->get()
            ->unique(fn (BlogPost $post) => Str::lower(trim((string) $post->title)))
            ->take(5)
            ->values();

        return view('blog.show', compact('blogPost', 'sidebarProfiles', 'countryHint', 'latestPosts'));
    }

    private function publishedPostsQuery(): Builder
    {
        return BlogPost::query()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    private function buildSidebarProfiles(?string $countryHint, int $limit = 6): Collection
    {
        $profiles = collect();

        if ($countryHint) {
            $profiles = $this->profilesBaseQuery()
                ->whereRaw('LOWER(TRIM(country)) = ?', [Str::lower(trim($countryHint))])
                ->take($limit)
                ->get();
        }

        if ($profiles->count() >= $limit) {
            return $profiles;
        }

        $remaining = $limit - $profiles->count();

        $fallback = $this->profilesBaseQuery()
            ->when(
                $profiles->isNotEmpty(),
                fn (Builder $query) => $query->whereNotIn('id', $profiles->pluck('id')->all())
            )
            ->take($remaining)
            ->get();

        return $profiles->concat($fallback)->values();
    }

    private function profilesBaseQuery(): Builder
    {
        $query = Profile::query()
            ->select([
                'id',
                'user_id',
                'display_id',
                'dob',
                'profession',
                'country',
                'city',
                'status',
                'verification_status',
            ])
            ->visibleToUsers()
            ->with(['primaryPhoto', 'user'])
            ->latest('created_at');

        $preferredGender = $this->preferredBrowseGender();
        if ($preferredGender) {
            $query->where('gender', $preferredGender);
        }

        $viewerProfile = auth()->user()?->profile;
        if ($viewerProfile) {
            $query->where('id', '!=', $viewerProfile->id);
        }

        return $query;
    }

    private function detectCountryFromText(string $text): ?string
    {
        $cleanText = Str::lower(trim($text));
        if ($cleanText === '') {
            return null;
        }

        $countries = \Illuminate\Support\Facades\Cache::remember('blog_countries_from_profiles_v1', 1800, function () {
            return Profile::query()
                ->visibleToUsers()
                ->whereNotNull('country')
                ->where('country', '!=', '')
                ->distinct()
                ->pluck('country')
                ->map(fn ($country) => trim((string) $country))
                ->filter()
                ->unique()
                ->sortByDesc(fn ($country) => strlen($country))
                ->values()
                ->all();
        });

        foreach ($countries as $country) {
            $needle = Str::lower($country);
            $pattern = '/(^|[^a-z0-9])' . preg_quote($needle, '/') . '([^a-z0-9]|$)/i';

            if (preg_match($pattern, $cleanText) === 1) {
                return $country;
            }
        }

        return null;
    }

    private function preferredBrowseGender(): ?string
    {
        $viewerGender = Str::lower((string) auth()->user()?->profile?->gender);

        if ($viewerGender === 'male') {
            return 'female';
        }

        if ($viewerGender === 'female') {
            return 'male';
        }

        return null;
    }
}
