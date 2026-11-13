<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterestRequest;
use App\Models\Block;
use App\Models\Interest;
use App\Models\Profile;
use App\Models\Thread;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InterestReceived;

class InterestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function send(InterestRequest $request)
    {
        $fromProfile = $request->user()->profile;
        if (!$fromProfile) {
            return redirect()->route('profile.photos');
        }

        if (!$request->user()->canMessage()) {
            return redirect()->route('packages.index')->with('error', 'Initiating chat is a premium feature. Please upgrade your plan.');
        }

        if ((string) $fromProfile->display_id === (string) $request->input('to_display_id')) {
            return redirect()->back()->withErrors(['interest' => 'You cannot send interest to yourself.']);
        }

        $toProfile = Profile::visibleToUsers()
            ->where('display_id', $request->input('to_display_id'))
            ->firstOrFail();

        $blocked = Block::where(function ($query) use ($fromProfile, $toProfile) {
            $query->where('blocker_profile_id', $fromProfile->id)
                ->where('blocked_profile_id', $toProfile->id);
        })->orWhere(function ($query) use ($fromProfile, $toProfile) {
            $query->where('blocker_profile_id', $toProfile->id)
                ->where('blocked_profile_id', $fromProfile->id);
        })->exists();

        if ($blocked) {
            return redirect()->back()->withErrors(['interest' => 'You cannot send interest to this profile.']);
        }

        $existing = Interest::where('from_profile_id', $fromProfile->id)
            ->where('to_profile_id', $toProfile->id)
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors(['interest' => 'Interest already sent.']);
        }

        if (!$this->withinDailyLimit($fromProfile)) {
            return redirect()->back()->withErrors(['interest' => 'Daily interest limit reached.']);
        }

        Interest::create([
            'from_profile_id' => $fromProfile->id,
            'to_profile_id' => $toProfile->id,
            'status' => 'pending',
        ]);

        // Trigger notification email
        if ($toProfile->user) {
            try {
                Mail::to($toProfile->user->email)->send(new InterestReceived($fromProfile));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->back()->with('status', 'Interest sent.');
    }

    public function accept(Interest $interest)
    {
        $profile = Auth::user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        $interest->loadMissing(['fromProfile.user', 'toProfile.user']);
        $hasAdminParticipant = ($interest->fromProfile?->user?->isAdmin() ?? false)
            || ($interest->toProfile?->user?->isAdmin() ?? false);
        if ($hasAdminParticipant && !Auth::user()->isAdmin()) {
            abort(404);
        }

        if ((int) $interest->to_profile_id !== (int) $profile->id) {
            abort(403);
        }

        if ($interest->status === 'accepted') {
            return redirect()->back()->with('status', 'Interest already accepted.');
        }

        $interest->status = 'accepted';
        $interest->save();

        $this->ensureThread($interest->from_profile_id, $interest->to_profile_id);

        $otherProfile = $interest->fromProfile;

        // Flash match info for animation
        session()->flash('match_animation', [
            'user_photo' => $profile->primaryPhoto ? asset('storage/' . $profile->primaryPhoto->path) : 'https://i.pravatar.cc/400?u=' . $profile->id,
            'match_photo' => $otherProfile->primaryPhoto ? asset('storage/' . $otherProfile->primaryPhoto->path) : 'https://i.pravatar.cc/400?u=' . $otherProfile->id,
            'match_name' => $otherProfile->first_name ?: 'User'
        ]);

        return redirect()->back()->with('status', 'Interest accepted.');
    }

    public function decline(Interest $interest)
    {
        $profile = Auth::user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        $interest->loadMissing(['fromProfile.user', 'toProfile.user']);
        $hasAdminParticipant = ($interest->fromProfile?->user?->isAdmin() ?? false)
            || ($interest->toProfile?->user?->isAdmin() ?? false);
        if ($hasAdminParticipant && !Auth::user()->isAdmin()) {
            abort(404);
        }

        if ((int) $interest->to_profile_id !== (int) $profile->id) {
            abort(403);
        }

        if ($interest->status === 'declined') {
            return redirect()->back()->with('status', 'Interest already declined.');
        }

        $interest->status = 'declined';
        $interest->save();

        return redirect()->back()->with('status', 'Interest declined.');
    }

    private function ensureThread(int $profileA, int $profileB): void
    {
        $a = min($profileA, $profileB);
        $b = max($profileA, $profileB);

        Thread::firstOrCreate([
            'profile_a_id' => $a,
            'profile_b_id' => $b,
        ]);
    }

    private function withinDailyLimit(Profile $profile): bool
    {
        $plan = $this->getCurrentPlan();
        $limits = is_array($plan?->limits_json) ? $plan->limits_json : [];
        $dailyLimit = max(1, (int) ($limits['daily_interests'] ?? config('limits.free.daily_interests', 5)));

        $todayCount = Interest::where('from_profile_id', $profile->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return $todayCount < $dailyLimit;
    }

    private function getCurrentPlan()
    {
        $user = Auth::user();
        if (!$user) {
            return \App\Models\Plan::where('name', 'Free')->first();
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_at')->orWhere('end_at', '>', now());
            })
            ->with('plan')
            ->first();

        return $subscription ? $subscription->plan : \App\Models\Plan::where('name', 'Free')->first();
    }
}
