<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class PremiumProfileController extends Controller
{
    /**
     * Display profiles with the highest-priced subscription (Abroad/Overseas package)
     */
    public function abroadProposals()
    {
        // Get the highest-priced plan
        $topPlan = Plan::where('is_active', true)
            ->orderBy('price', 'desc')
            ->first();
        $profiles = $this->premiumProfilesByPlan($topPlan);

        $viewerProfile = auth()->user()?->profile;
        if ($viewerProfile) {
            $viewerProfile->loadMissing('preferences');
            foreach ($profiles as $profile) {
                $profile->match_percentage = $profile->calculateMatchPercentage($viewerProfile);
            }
        }

        return view('premium.abroad-proposals', [
            'profiles' => $profiles,
            'planName' => $topPlan->name ?? 'Overseas Package'
        ]);
    }

    /**
     * Display profiles with the second-highest priced subscription (Elite/Upper Class package)
     */
    public function eliteMatrimonial()
    {
        // Get the second-highest priced plan
        $elitePlan = Plan::where('is_active', true)
            ->orderBy('price', 'desc')
            ->skip(1)
            ->first();
        $profiles = $this->premiumProfilesByPlan($elitePlan);

        $viewerProfile = auth()->user()?->profile;
        if ($viewerProfile) {
            $viewerProfile->loadMissing('preferences');
            foreach ($profiles as $profile) {
                $profile->match_percentage = $profile->calculateMatchPercentage($viewerProfile);
            }
        }

        return view('premium.elite-matrimonial', [
            'profiles' => $profiles,
            'planName' => $elitePlan->name ?? 'Elite Package'
        ]);
    }

    private function preferredBrowseGender(): ?string
    {
        $viewerGender = strtolower((string) auth()->user()?->profile?->gender);

        if ($viewerGender === 'male') {
            return 'female';
        }

        if ($viewerGender === 'female') {
            return 'male';
        }

        return null;
    }

    private function premiumProfilesByPlan(?Plan $plan): LengthAwarePaginator
    {
        if (!$plan) {
            return $this->emptyProfilePaginator();
        }

        return Profile::query()
            ->whereHas('user.subscriptions', function ($query) use ($plan) {
                $query->where('plan_id', $plan->id)
                    ->where('status', 'active')
                    ->where('end_at', '>', now());
            })
            ->when($this->preferredBrowseGender(), fn (Builder $query, string $gender) => $query->where('gender', $gender))
            ->visibleToUsers()
            ->with(['primaryPhoto', 'user.activeSubscription.plan'])
            ->when(auth()->user()?->profile, fn (Builder $query, $viewerProfile) => $query->where('id', '!=', $viewerProfile->id))
            ->paginate(12)
            ->withQueryString();
    }

    private function emptyProfilePaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            [],
            0,
            12,
            LengthAwarePaginator::resolveCurrentPage(),
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
