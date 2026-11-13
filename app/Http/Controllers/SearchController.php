<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Block;
use App\Models\Profile;
use App\Models\SavedSearch;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(SearchRequest $request)
    {
        $filters = $request->validated();
        $query = Profile::query()
            ->select([
                'id',
                'user_id',
                'display_id',
                'gender',
                'dob',
                'height_cm',
                'marital_status',
                'education',
                'profession',
                'sect',
                'city',
                'state',
                'country',
                'religion',
                'caste',
                'cnic_verified',
                'status',
                'verification_status',
                'mother_tongue',
                'completion_score',
                'created_at',
                'last_active_at',
            ])
            ->with(['primaryPhoto', 'user.activeSubscription.plan'])
            ->visibleToUsers()
            ->whereHas('user.activeSubscription');


        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('profession', 'like', "%{$keyword}%")
                    ->orWhere('education', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhere('country', 'like', "%{$keyword}%")
                    ->orWhere('sect', 'like', "%{$keyword}%")
                    ->orWhere('display_id', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['display_id'])) {
            $query->where('display_id', 'like', '%' . $filters['display_id'] . '%');
        }

        if (!empty($filters['gender']) && $filters['gender'] !== 'all') {
            $query->where('gender', $filters['gender']);
        } elseif (empty($filters['gender']) && Auth::check() && Auth::user()->profile) {
            $oppositeGender = Auth::user()->profile->gender === 'male' ? 'female' : 'male';
            $query->where('gender', $oppositeGender);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['mother_tongue'])) {
            $query->where('mother_tongue', $filters['mother_tongue']);
        }

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        if (!empty($filters['sect'])) {
            $query->where('sect', $filters['sect']);
        }

        if (!empty($filters['marital_status'])) {
            $query->where('marital_status', $filters['marital_status']);
        }

        if (!empty($filters['education'])) {
            $query->where('education', $filters['education']);
        }

        if (!empty($filters['profession'])) {
            $query->where('profession', $filters['profession']);
        }

        if (!empty($filters['age_min']) || !empty($filters['age_max'])) {
            $min = $filters['age_min'] ?? 18;
            $max = $filters['age_max'] ?? 70;
            $query->whereBetween('dob', [now()->subYears($max), now()->subYears($min)]);
        }

        if (!empty($filters['height_min']) || !empty($filters['height_max'])) {
            $min = $filters['height_min'] ?? 120;
            $max = $filters['height_max'] ?? 220;
            $query->whereBetween('height_cm', [$min, $max]);
        }

        if (!empty($filters['sort'])) {
            if ($filters['sort'] === 'recent') {
                $query->orderByDesc('last_active_at');
            } elseif ($filters['sort'] === 'newest') {
                $query->orderByDesc('created_at');
            }
        } else {
            $query->orderByDesc('created_at');
        }

        // --- Newly Added Filters ---

        if (!empty($filters['verified_only'])) {
            $query->where('cnic_verified', true);
        }

        if (!empty($filters['divorced'])) {
            $query->where('marital_status', 'Divorced');
        }

        if (!empty($filters['second_marriage'])) {
            // Assuming 'Second Marriage' is the string stored in DB
            $query->where('marital_status', 'Second Marriage');
        }

        // Added Missing Filters
        if (!empty($filters['caste'])) {
            $query->where('caste', $filters['caste']);
        }

        if (!empty($filters['religion'])) {
            $query->where('religion', $filters['religion']);
        }

        if (!empty($filters['plan'])) {
            $plans = $filters['plan']; // array of slugs/names like ['bronze', 'silver']
            $query->whereHas('user.subscriptions', function ($q) use ($plans) {
                $q->where('status', 'active')
                    ->whereHas('plan', function ($pq) use ($plans) {
                        // Match name logic: 'bronze' -> 'Bronze'
                        $pq->whereIn('name', array_map('ucfirst', $plans))
                            ->orWhereIn('name', $plans); // Fallback
                    });
            });
        }

        $viewerProfile = Auth::user()?->profile;
        if ($viewerProfile) {
            $query->where('id', '!=', $viewerProfile->id);
            $blockedIds = $this->blockedProfileIds((int) $viewerProfile->id);
            if ($blockedIds !== []) {
                $query->whereNotIn('id', $blockedIds);
            }
        }

        $profiles = $query->paginate(12)->withQueryString();

        if ($viewerProfile) {
            $viewerProfile->loadMissing('preferences'); // Eager load preferences once
            foreach ($profiles as $profile) {
                $profile->match_percentage = $profile->calculateMatchPercentage($viewerProfile);
            }
        }

        $savedSearches = $viewerProfile
            ? SavedSearch::where('profile_id', $viewerProfile->id)->get()
            : collect();

        return view('profiles.index', compact('profiles', 'filters', 'savedSearches'));
    }

    private function blockedProfileIds(int $profileId): array
    {
        return Block::query()
            ->where(function ($query) use ($profileId) {
                $query->where('blocker_profile_id', $profileId)
                    ->orWhere('blocked_profile_id', $profileId);
            })
            ->selectRaw(
                'CASE WHEN blocker_profile_id = ? THEN blocked_profile_id ELSE blocker_profile_id END AS blocked_id',
                [$profileId]
            )
            ->pluck('blocked_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}
