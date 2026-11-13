<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Profile;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(string $displayId)
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        if ((string) $profile->display_id === $displayId) {
            return redirect()->back()->withErrors(['favorite' => 'You cannot favorite your own profile.']);
        }

        $target = Profile::visibleToUsers()
            ->where('display_id', $displayId)
            ->firstOrFail();

        Favorite::firstOrCreate([
            'profile_id' => $profile->id,
            'target_profile_id' => $target->id,
        ]);

        return redirect()->back()->with('status', 'Added to favorites.');
    }

    public function index()
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        $favorites = Favorite::with(['targetProfile.primaryPhoto', 'targetProfile.user.activeSubscription.plan'])
            ->whereHas('targetProfile.user', function ($q) {
                $q->nonAdmin();
            })
            ->where('profile_id', $profile->id)
            ->latest()
            ->paginate(12);

        // Match = how well target profile fits logged-in user's partner requirements.
        $profile->loadMissing('preferences');
        foreach ($favorites as $favorite) {
            if ($favorite->targetProfile) {
                $favorite->targetProfile->match_percentage = $favorite->targetProfile->calculateMatchPercentage($profile);
            }
        }

        return view('favorites.index', compact('favorites'));
    }

    public function destroy(string $displayId)
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        $target = Profile::where('display_id', $displayId)
            ->whereHas('user', function ($q) {
                $q->nonAdmin();
            })
            ->firstOrFail();

        Favorite::where('profile_id', $profile->id)
            ->where('target_profile_id', $target->id)
            ->delete();

        return redirect()->back()->with('status', 'Removed from favorites.');
    }
}
