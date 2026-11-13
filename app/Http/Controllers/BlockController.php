<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Profile;

class BlockController extends Controller
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
            return redirect()->back()->withErrors(['block' => 'You cannot block your own profile.']);
        }

        $target = Profile::visibleToUsers()
            ->where('display_id', $displayId)
            ->firstOrFail();

        Block::firstOrCreate([
            'blocker_profile_id' => $profile->id,
            'blocked_profile_id' => $target->id,
        ]);

        return redirect()->back()->with('status', 'User blocked.');
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

        Block::where('blocker_profile_id', $profile->id)
            ->where('blocked_profile_id', $target->id)
            ->delete();

        return redirect()->back()->with('status', 'Block removed.');
    }
}
