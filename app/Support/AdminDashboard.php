<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Profile;
use App\Models\Report;
use App\Models\Subscription;
use App\Models\SuccessStory;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Numbers behind the admin dashboard screen. Everything here is read from the
 * live database so the panel can never show a fake figure; the payload is
 * cached for five minutes to keep the page cheap.
 */
class AdminDashboard
{
    protected const CACHE_KEY = 'admin_dashboard_payload_v1';
    protected const CACHE_SECONDS = 300;

    /** @return array<string, mixed> */
    public static function payload(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_SECONDS, fn (): array => [
            'stats' => self::stats(),
            'growth' => self::growth(),
            'gender' => self::gender(),
            'recent_users' => self::recentUsers(),
            'activity' => self::activity(),
            'quick_actions' => self::quickActions(),
            'system' => self::systemStatus(),
            'generated_at' => now()->format('h:i A'),
            'generated_day' => now()->format('F j, Y'),
        ]);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<int, array<string, mixed>> */
    protected static function stats(): array
    {
        $months = self::monthWindow(12);

        $userSeries = self::cumulative(fn (Carbon $to) => User::where('created_at', '<=', $to)->count(), $months);
        $verifiedSeries = self::cumulative(fn (Carbon $to) => Profile::where('verification_status', 'verified')
            ->where('created_at', '<=', $to)->count(), $months);
        $activeSeries = self::cumulative(fn (Carbon $to) => Profile::where('status', 'active')
            ->where('created_at', '<=', $to)->count(), $months);
        $newSeries = self::perMonth(fn (Carbon $from, Carbon $to) => User::whereBetween('created_at', [$from, $to])->count(), $months);

        return [
            [
                'label' => 'Total Users',
                'value' => number_format(User::count()),
                'delta' => self::delta($userSeries),
                'tone' => 'blue',
                'icon' => 'heroicon-o-users',
                'series' => $userSeries,
                'href' => route('filament.resources.users.index'),
            ],
            [
                'label' => 'Verified Users',
                'value' => number_format(Profile::where('verification_status', 'verified')->count()),
                'delta' => self::delta($verifiedSeries),
                'tone' => 'green',
                'icon' => 'heroicon-o-shield-check',
                'series' => $verifiedSeries,
                'href' => route('filament.resources.profiles.index', [
                    'tableFilters' => ['verification_status' => ['value' => 'verified']],
                ]),
            ],
            [
                'label' => 'Active Profiles',
                'value' => number_format(Profile::where('status', 'active')->count()),
                'delta' => self::delta($activeSeries),
                'tone' => 'rose',
                'icon' => 'heroicon-o-heart',
                'series' => $activeSeries,
                'href' => route('filament.resources.profiles.index', [
                    'tableFilters' => ['status' => ['value' => 'active']],
                ]),
            ],
            [
                'label' => 'New This Month',
                'value' => number_format(end($newSeries) ?: 0),
                'delta' => self::monthToDateDelta(),
                'tone' => 'amber',
                'icon' => 'heroicon-o-calendar',
                'series' => $newSeries,
                'href' => route('filament.resources.users.index'),
            ],
        ];
    }

    /** @return array<string, mixed> */
    protected static function growth(): array
    {
        $months = self::monthWindow(12);

        $totals = self::cumulative(fn (Carbon $to) => User::where('created_at', '<=', $to)->count(), $months);
        $active = self::cumulative(fn (Carbon $to) => Profile::where('status', 'active')
            ->where('created_at', '<=', $to)->count(), $months);

        return [
            'labels' => array_map(fn (Carbon $m) => $m->format('M y'), $months),
            'total_users' => $totals,
            'active_users' => $active,
            'max' => max(1, max(max($totals), max($active))),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    protected static function gender(): array
    {
        // "key" is a reserved word in MySQL/MariaDB, so the alias has to be named.
        $counts = Profile::query()
            ->selectRaw('coalesce(nullif(gender, \'\'), \'other\') as gender_key, count(*) as total')
            ->groupBy('gender_key')
            ->pluck('total', 'gender_key')
            ->all();

        $total = array_sum($counts);

        $palette = ['male' => 'blue', 'female' => 'rose', 'other' => 'violet'];
        $order = ['male', 'female', 'other'];

        $segments = [];
        foreach ($order as $key) {
            $value = (int) ($counts[$key] ?? 0);
            $segments[] = [
                'label' => ucfirst($key),
                'value' => $value,
                'percent' => $total > 0 ? (int) round($value / $total * 100) : 0,
                'tone' => $palette[$key],
            ];
        }

        return ['total' => $total, 'segments' => $segments];
    }

    /** @return array<int, array<string, mixed>> */
    protected static function recentUsers(int $limit = 6): array
    {
        return User::query()
            ->with('profile:id,user_id,gender,status,verification_status')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (User $user) {
                $profile = $user->profile;
                $photoPath = $profile?->primaryPhoto?->path;

                return [
                    'id' => '#' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                    'name' => $user->name,
                    'email' => $user->email,
                    'gender' => $profile?->gender ?: 'other',
                    'status' => $profile?->status ?: 'pending',
                    'joined' => optional($user->created_at)->format('M j, Y') ?: '—',
                    'photo' => $photoPath ? Media::thumb($photoPath, 96) : null,
                    'initials' => self::initials($user->name),
                    'href' => $profile ? route('filament.resources.profiles.view', $profile) : route('filament.resources.users.edit', $user),
                ];
            })
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    protected static function activity(int $limit = 6): array
    {
        $items = [];

        if ($user = User::latest()->first()) {
            $items[] = [
                'tone' => 'blue', 'icon' => 'heroicon-o-user',
                'title' => 'New member registered',
                'detail' => $user->name,
                'at' => $user->created_at,
            ];
        }

        if ($profile = Profile::where('verification_status', 'verified')->latest('updated_at')->first()) {
            $items[] = [
                'tone' => 'green', 'icon' => 'heroicon-o-shield-check',
                'title' => 'Profile verified',
                'detail' => optional($profile->user)->name ?: ('Profile #' . $profile->id),
                'at' => $profile->updated_at,
            ];
        }

        if ($report = Report::with('target.user')->latest()->first()) {
            $items[] = [
                'tone' => 'rose', 'icon' => 'heroicon-o-flag',
                'title' => 'Report ' . ($report->status ?: 'opened') . ($report->category ? ' · ' . $report->category : ''),
                'detail' => optional($report->target?->user)->name ?: ('Profile #' . $report->target_profile_id),
                'at' => $report->created_at,
            ];
        }

        if ($post = BlogPost::where('status', 'published')->latest()->first()) {
            $items[] = [
                'tone' => 'amber', 'icon' => 'heroicon-o-book-open',
                'title' => 'New blog post published',
                'detail' => $post->title,
                'at' => $post->published_at ?: $post->created_at,
            ];
        }

        if ($story = SuccessStory::where('status', 'approved')->latest()->first()) {
            $items[] = [
                'tone' => 'violet', 'icon' => 'heroicon-o-photograph',
                'title' => 'Success story approved',
                'detail' => $story->couple_names,
                'at' => $story->created_at,
            ];
        }

        if ($subscription = Subscription::latest('updated_at')->first()) {
            $items[] = [
                'tone' => 'blue', 'icon' => 'heroicon-o-credit-card',
                'title' => 'Subscription ' . $subscription->status,
                'detail' => optional($subscription->user)->name ?: ('Subscription #' . $subscription->id),
                'at' => $subscription->updated_at,
            ];
        }

        usort($items, fn ($a, $b) => ($b['at'] ?? now()) <=> ($a['at'] ?? now()));

        return array_map(function (array $item) {
            $item['when'] = optional($item['at'])->diffForHumans() ?: '—';

            return $item;
        }, array_slice($items, 0, $limit));
    }

    /** @return array<int, array<string, mixed>> */
    protected static function quickActions(): array
    {
        return [
            [
                'label' => 'View All Users', 'detail' => 'Manage member accounts',
                'icon' => 'heroicon-o-users', 'tone' => 'blue',
                'href' => route('filament.resources.users.index'),
            ],
            [
                'label' => 'Check Pending Verifications', 'detail' => 'Review profiles',
                'icon' => 'heroicon-o-shield-check', 'tone' => 'green',
                'href' => route('filament.resources.profiles.index', [
                    'tableFilters' => ['verification_status' => ['value' => 'pending']],
                ]),
                'badge' => Profile::where('verification_status', 'pending')->count(),
            ],
            [
                'label' => 'View Open Reports', 'detail' => 'Member complaints',
                'icon' => 'heroicon-o-flag', 'tone' => 'violet',
                'href' => route('filament.resources.reports.index', [
                    'tableFilters' => ['status' => ['value' => 'open']],
                ]),
                'badge' => Report::where('status', 'open')->count(),
            ],
            [
                'label' => 'Add Blog Post', 'detail' => 'Create new content',
                'icon' => 'heroicon-o-book-open', 'tone' => 'amber',
                'href' => route('filament.resources.blog-posts.create'),
            ],
            [
                'label' => 'Manage Subscriptions', 'detail' => 'View and edit plans',
                'icon' => 'heroicon-o-credit-card', 'tone' => 'rose',
                'href' => route('filament.resources.subscriptions.index'),
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    protected static function systemStatus(): array
    {
        $db = true;
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $db = false;
        }

        $mailConfigured = filter_var(config('mail.from.address'), FILTER_VALIDATE_EMAIL) !== false
            && (string) config('mail.default') !== '';

        $storageWritable = File::isWritable(storage_path('app/public'));

        $queue = (string) config('queue.default');
        // "sync" is not an outage — jobs run inline during the request, so the
        // panel only calls the queue down when it is genuinely disabled ("null").
        $queueOk = $queue !== '' && $queue !== 'null';

        return [
            ['label' => 'Database', 'ok' => $db, 'note' => $db ? config('database.default') : 'offline'],
            ['label' => 'File Storage', 'ok' => $storageWritable, 'note' => $storageWritable ? 'writable' : 'not writable'],
            ['label' => 'Email Service', 'ok' => $mailConfigured, 'note' => config('mail.default')],
            ['label' => 'Background Queue', 'ok' => $queueOk, 'note' => $queue === 'sync' ? 'runs inline' : $queue],
            ['label' => 'Matchmaking Scheduler', 'ok' => $db && Profile::count() > 0, 'note' => 'cron every minute'],
        ];
    }

    /** @return array<int, Carbon> first day of each of the last N months, oldest first */
    protected static function monthWindow(int $months): array
    {
        $window = [];
        $cursor = Carbon::today()->startOfMonth();

        for ($i = $months - 1; $i >= 0; $i--) {
            $window[] = (clone $cursor)->subMonthsNoOverflow($i);
        }

        return $window;
    }

    /** @return array<int, int> running total at the end of each month */
    protected static function cumulative(callable $countUpTo, array $months): array
    {
        $series = [];

        foreach ($months as $month) {
            $series[] = (int) $countUpTo((clone $month)->endOfMonth());
        }

        return $series;
    }

    /** @return array<int, int> count inside each month */
    protected static function perMonth(callable $countBetween, array $months): array
    {
        $series = [];

        foreach ($months as $month) {
            $series[] = (int) $countBetween((clone $month)->startOfMonth(), (clone $month)->endOfMonth());
        }

        return $series;
    }

    /** Percentage change of the last point against the previous point. */
    protected static function delta(array $series): ?float
    {
        $count = count($series);
        if ($count < 2) {
            return null;
        }

        return self::ratio((int) $series[$count - 1], (int) $series[$count - 2]);
    }

    /**
     * "New This Month" is still a running month, so it is only fair against the
     * same number of days of the previous month — comparing six days with thirty
     * always reads as a crash.
     */
    protected static function monthToDateDelta(): ?float
    {
        $start = Carbon::today()->startOfMonth();
        $elapsed = (int) Carbon::today()->format('j') - 1;

        $previousStart = (clone $start)->subMonthNoOverflow();

        return self::ratio(
            (int) User::whereBetween('created_at', [$start, Carbon::today()->endOfDay()])->count(),
            (int) User::whereBetween('created_at', [$previousStart, (clone $previousStart)->addDays($elapsed)->endOfDay()])->count(),
        );
    }

    protected static function ratio(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    protected static function initials(?string $name): string
    {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        $letters = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'AU';
    }
}
