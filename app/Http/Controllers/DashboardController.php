<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\Thread;
use App\Services\ProfileCompletionService;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(ProfileCompletionService $completionService)
    {
        $user = auth()->user();
        $user->loadMissing('profile');
        $profile = $user->profile;

        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        // Keep stored score in sync with live completion checks so UI never shows stale percentages.
        $calculatedCompletion = $completionService->calculate($profile);
        if ((int) $profile->completion_score !== $calculatedCompletion) {
            $profile->forceFill(['completion_score' => $calculatedCompletion])->saveQuietly();
            $profile->completion_score = $calculatedCompletion;
        }

        // Eager-load related profiles for display
        $interestsReceived = Interest::with('fromProfile.primaryPhoto')
            ->where('to_profile_id', $profile->id)
            ->whereHas('fromProfile.user', function ($q) {
                $q->nonAdmin();
            })
            ->latest()
            ->take(5)
            ->get();

        // Eager-load both profiles for thread display
        $threads = Thread::with(['profileA.primaryPhoto', 'profileB.primaryPhoto'])
            ->where(function ($q) use ($profile) {
                $q->where('profile_a_id', $profile->id)
                    ->orWhere('profile_b_id', $profile->id);
            })
            ->whereHas('profileA.user', function ($q) {
                $q->nonAdmin();
            })
            ->whereHas('profileB.user', function ($q) {
                $q->nonAdmin();
            })
            ->latest()
            ->take(5)
            ->get();

        // Mark unread interests as read (single bulk update)
        Interest::where('to_profile_id', $profile->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $prompts = $completionService->prompts($profile);
        $pendingItems = $completionService->pendingItems($profile);

        // Exclude rejected profiles from matches
        $oppositeGender = $profile->gender === 'male' ? 'female' : 'male';
        $matches = \App\Models\Profile::with(['primaryPhoto', 'user.activeSubscription.plan'])
            ->where('gender', $oppositeGender)
            ->visibleToUsers()
            ->where('id', '!=', $profile->id)
            ->latest()
            ->take(4)
            ->get();

        // Add match ratio for card badge.
        $profile->loadMissing('preferences');
        foreach ($matches as $match) {
            $match->match_percentage = $match->calculateMatchPercentage($profile);
        }

        // Fetch user's favorites
        $myFavorites = \App\Models\Favorite::with(['targetProfile.primaryPhoto', 'targetProfile.user.activeSubscription.plan'])
            ->where('profile_id', $profile->id)
            ->whereHas('targetProfile.user', function($q) {
                $q->nonAdmin();
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($favorite) use ($profile) {
                $target = $favorite->targetProfile;
                if ($target) {
                    $target->match_percentage = $target->calculateMatchPercentage($profile);
                }
                return $target;
            })->filter();

        // Single query for subscription
        $latestSubscription = $user->subscriptions()->with('plan')->latest()->first();
        $dashboardGreeting = $this->resolveDashboardGreeting();
        $dashboardDisplayName = trim((string) ($profile?->first_name ?: $user->first_name ?: $user->name ?: 'Member'));
        $dashboardQuote = $this->resolveDashboardQuote();

        return view('dashboard', compact(
            'profile',
            'interestsReceived',
            'threads',
            'prompts',
            'pendingItems',
            'matches',
            'myFavorites',
            'latestSubscription',
            'dashboardGreeting',
            'dashboardDisplayName',
            'dashboardQuote'
        ));
    }

    private function resolveDashboardGreeting(): string
    {
        $hour = (int) now()->format('G');

        if ($hour >= 5 && $hour < 12) {
            return 'Good Morning';
        }

        if ($hour >= 12 && $hour < 17) {
            return 'Good Afternoon';
        }

        if ($hour >= 17 && $hour < 21) {
            return 'Good Evening';
        }

        return 'Good Night';
    }

    private function resolveDashboardQuote(): string
    {
        $quotes = $this->dashboardQuotes();
        $pool = session('dashboard_quote_pool');
        $lastIndex = session('dashboard_last_quote_index');

        if (!is_array($pool) || count($pool) === 0) {
            $pool = array_keys($quotes);
            shuffle($pool);

            if ($lastIndex !== null && count($pool) > 1 && $pool[0] === (int) $lastIndex) {
                $swapIndex = 1;
                [$pool[0], $pool[$swapIndex]] = [$pool[$swapIndex], $pool[0]];
            }
        }

        $currentIndex = array_shift($pool);
        session([
            'dashboard_quote_pool' => $pool,
            'dashboard_last_quote_index' => $currentIndex,
        ]);

        return $quotes[$currentIndex] ?? $quotes[0];
    }

    private function dashboardQuotes(): array
    {
        return [
            'Great matches begin with honest intentions and respectful conversation.',
            'Every meaningful connection starts with one thoughtful message.',
            'Patience in search leads to confidence in commitment.',
            'Your profile tells a story. Keep it real, clear, and kind.',
            'Compatibility grows when values, trust, and timing align.',
            'Small steps daily can bring you closer to the right partner.',
            'Respect and sincerity are always more attractive than perfection.',
            'The right connection feels calm, not confusing.',
            'Good communication turns interest into understanding.',
            'Stay hopeful. The right profile can appear at the right time.',
            'Strong relationships are built on shared values and mutual effort.',
            'Kind words today can become lifelong memories tomorrow.',
        ];
    }
}
