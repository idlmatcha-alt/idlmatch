<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Favorite;
use App\Models\Interest;
use App\Models\Profile;
use App\Models\ProfileView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MemberProfileController extends Controller
{
    public function show(string $displayId)
    {
        // Single query with counts and eager loads
        $query = Profile::with(['photos', 'user.activeSubscription.plan', 'preferences'])
            ->withCount([
                'interestsReceived as interest_count',
            ])
            ->where('display_id', $displayId);

        // Never expose admin-owned profiles on public/user side routes.
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $query->whereHas('user', function ($q) {
                $q->nonAdmin();
            });
        }

        $profile = $query->firstOrFail();

        // Count views and favorites separately (different FK patterns)
        $viewCount = ProfileView::where('viewed_id', $profile->id)->count();
        $favoriteCount = Favorite::where('target_profile_id', $profile->id)->count();

        if ($profile->status !== 'active' && (!Auth::user() || (int) Auth::user()->id !== (int) $profile->user_id)) {
            abort(404);
        }

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('profile');
        }
        $viewerProfile = $user?->profile;
        $mutualInterest = false;

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('profile');
        }
        $viewerProfile = $user?->profile;
        $mutualInterest = false;
        
        // Check if viewed profile has premium (for badge)
        $profilePremium = $profile->user?->isVerifiedMember() ?? false;
        $emailVerified = $profile->user?->email_verified_at !== null;
        $interestCount = $profile->interest_count ?? 0;

        $isFavorited = false;

        $blockedIds = [];
        if ($user && $viewerProfile) {
            $blockedIds = $this->blockedProfileIds((int) $viewerProfile->id);
            if (in_array((int) $profile->id, $blockedIds, true)) {
                abort(403);
            }

            // Check view limits if not own profile
            if (!$user->canViewProfile($profile)) {
                return redirect()->route('packages.index')->with('error', 'You have reached your profile view limit. Please upgrade your plan.');
            }

            // Record view
            $user->recordProfileView($profile);

            $mutualInterest = Interest::where(function ($query) use ($viewerProfile, $profile) {
                $query->where('from_profile_id', $viewerProfile->id)
                    ->where('to_profile_id', $profile->id)
                    ->where('status', 'accepted');
            })->orWhere(function ($query) use ($viewerProfile, $profile) {
                $query->where('from_profile_id', $profile->id)
                    ->where('to_profile_id', $viewerProfile->id)
                    ->where('status', 'accepted');
            })->exists();

            $isFavorited = Favorite::where('profile_id', $viewerProfile->id)
                ->where('target_profile_id', $profile->id)
                ->exists();
        }

        $canMessage = $user && $user->canMessage() && $mutualInterest;

        $visiblePhotos = (!$user || (int) $user->id !== (int) $profile->user_id)
            ? $profile->photos->where('status', 'approved')
            : $profile->photos;
        $profile->setRelation('photos', $this->sortGalleryPhotos($visiblePhotos));

        // Calculate match percentage if viewer has a profile
        if ($viewerProfile) {
            $viewerProfile->loadMissing('preferences');
            $profile->match_percentage = $profile->calculateMatchPercentage($viewerProfile);
        }

        $relatedProfiles = $this->getRelatedProfiles($profile, $viewerProfile, $blockedIds);

        // Check if viewer is a paid member
        $isPaidMember = false;
        if ($user) {
            $activeSub = $user->getActiveSubscription();
            if ($activeSub && $activeSub->plan && $activeSub->plan->price > 0 && 
                ($activeSub->end_at === null || $activeSub->end_at > now())) {
                $isPaidMember = true;
            }
        }

        $mobileGalleryUploadUrl = null;
        if ($user && (int) $user->id === (int) $profile->user_id) {
            $tokenCacheKey = 'mobile_upload_token_' . $user->id;
            $mobileUploadToken = (string) Cache::get($tokenCacheKey, '');

            if ($mobileUploadToken === '') {
                $mobileUploadToken = Str::random(32);
            }

            Cache::put($tokenCacheKey, $mobileUploadToken, now()->addHour());

            $mobileGalleryUploadUrl = route('profile.mobile-upload', [
                'user' => $user->id,
                'token' => $mobileUploadToken,
                'type' => 'gallery',
            ]);
        }

        return view('profiles.show', compact('profile', 'viewCount', 'interestCount', 'favoriteCount', 'canMessage',
            'mutualInterest',
            'isFavorited',
            'profilePremium',
            'emailVerified',
            'isPaidMember',
            'mobileGalleryUploadUrl',
            'relatedProfiles'
        ));
    }

    private function getRelatedProfiles(Profile $profile, ?Profile $viewerProfile, array $blockedIds = []): Collection
    {
        $baseQuery = Profile::query()
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
            ->where('id', '!=', $profile->id);

        $preferredGender = $this->resolvePreferredBrowseGender($viewerProfile, $profile);
        if ($preferredGender) {
            $baseQuery->where('gender', $preferredGender);
        }

        if ($viewerProfile) {
            $baseQuery->where('id', '!=', $viewerProfile->id);
            if ($blockedIds !== []) {
                $baseQuery->whereNotIn('id', $blockedIds);
            }
        }

        $primaryQuery = (clone $baseQuery);
        if (!empty($profile->city)) {
            $primaryQuery->orderByRaw('CASE WHEN city = ? THEN 0 ELSE 1 END', [$profile->city]);
        }
        if (!empty($profile->country)) {
            $primaryQuery->orderByRaw('CASE WHEN country = ? THEN 0 ELSE 1 END', [$profile->country]);
        }

        $relatedProfiles = $primaryQuery
            ->orderByDesc('last_active_at')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        if ($relatedProfiles->count() < 4) {
            $excludeIds = $relatedProfiles->pluck('id')->push($profile->id);
            if ($viewerProfile) {
                $excludeIds->push($viewerProfile->id);
            }

            $fallbackProfiles = (clone $baseQuery)
                ->whereNotIn('id', $excludeIds->unique()->all())
                ->orderByDesc('last_active_at')
                ->orderByDesc('created_at')
                ->limit(4 - $relatedProfiles->count())
                ->get();

            $relatedProfiles = $relatedProfiles->concat($fallbackProfiles)->values();
        }

        if ($viewerProfile) {
            $viewerProfile->loadMissing('preferences');
            foreach ($relatedProfiles as $relatedProfile) {
                $relatedProfile->match_percentage = $relatedProfile->calculateMatchPercentage($viewerProfile);
            }
        }

        return $relatedProfiles;
    }

    private function resolvePreferredBrowseGender(?Profile $viewerProfile, Profile $contextProfile): ?string
    {
        if ($viewerProfile) {
            $viewerGender = strtolower((string) $viewerProfile->gender);

            if ($viewerGender === 'male') {
                return 'female';
            }

            if ($viewerGender === 'female') {
                return 'male';
            }

            return null;
        }

        $contextGender = strtolower((string) $contextProfile->gender);
        return in_array($contextGender, ['male', 'female'], true) ? $contextGender : null;
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

    private function sortGalleryPhotos(Collection $photos): Collection
    {
        return $photos->sort(function ($left, $right) {
            $primaryCompare = ((int) $right->is_primary) <=> ((int) $left->is_primary);
            if ($primaryCompare !== 0) {
                return $primaryCompare;
            }

            $rightTimestamp = optional($right->created_at)->getTimestamp() ?? 0;
            $leftTimestamp = optional($left->created_at)->getTimestamp() ?? 0;
            return $rightTimestamp <=> $leftTimestamp;
        })->values();
    }
}
