<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\Thread;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMessageReceived;


class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Thread $thread)
    {
        $user = auth()->user();
        $user->loadMissing('profile');
        $profile = $user->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        $thread->loadMissing(['profileA.user', 'profileB.user']);
        $hasAdminParticipant = ($thread->profileA?->user?->isAdmin() ?? false)
            || ($thread->profileB?->user?->isAdmin() ?? false);
        if ($hasAdminParticipant && !$user->isAdmin()) {
            abort(404);
        }

        if (!in_array((int) $profile->id, [(int) $thread->profile_a_id, (int) $thread->profile_b_id], true)) {
            abort(403);
        }

        $hasAccepted = \App\Models\Interest::where(function ($query) use ($thread) {
            $query->where('from_profile_id', $thread->profile_a_id)
                ->where('to_profile_id', $thread->profile_b_id)
                ->where('status', 'accepted');
        })->orWhere(function ($query) use ($thread) {
            $query->where('from_profile_id', $thread->profile_b_id)
                ->where('to_profile_id', $thread->profile_a_id)
                ->where('status', 'accepted');
        })->exists();

        if (!$hasAccepted) {
            abort(403);
        }

        $query = $thread->messages()->with('sender');

        if (request()->filled('after')) {
            $query->where('created_at', '>', request('after'));
        }

        return response()->json($query->orderBy('created_at')->get());
    }

    public function store(MessageRequest $request, Thread $thread)
    {
        $user = $request->user();
        $user->loadMissing('profile');
        $profile = $user->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        $thread->loadMissing(['profileA.user', 'profileB.user']);
        $hasAdminParticipant = ($thread->profileA?->user?->isAdmin() ?? false)
            || ($thread->profileB?->user?->isAdmin() ?? false);
        if ($hasAdminParticipant && !$user->isAdmin()) {
            abort(404);
        }

        if (!in_array((int) $profile->id, [(int) $thread->profile_a_id, (int) $thread->profile_b_id], true)) {
            abort(403);
        }

        $hasAccepted = \App\Models\Interest::where(function ($query) use ($thread) {
            $query->where('from_profile_id', $thread->profile_a_id)
                ->where('to_profile_id', $thread->profile_b_id)
                ->where('status', 'accepted');
        })->orWhere(function ($query) use ($thread) {
            $query->where('from_profile_id', $thread->profile_b_id)
                ->where('to_profile_id', $thread->profile_a_id)
                ->where('status', 'accepted');
        })->exists();

        if (!$hasAccepted) {
            return redirect()->back()->withErrors(['message' => 'Messaging is available after mutual interest.']);
        }

        if (!$this->withinDailyLimit($profile->id)) {
            return redirect()->back()->withErrors(['message' => 'Daily message limit reached.']);
        }

        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_profile_id' => $profile->id,
            'body' => $request->input('body'),
        ]);

        $thread->last_message_at = $message->created_at;
        $thread->save();

        // Trigger notification email
        $recipientProfileId = ($thread->profile_a_id == $profile->id) ? $thread->profile_b_id : $thread->profile_a_id;
        $recipientProfile = \App\Models\Profile::with('user')->find($recipientProfileId);

        if ($recipientProfile && $recipientProfile->user) {
            try {
                Mail::to($recipientProfile->user->email)->send(new NewMessageReceived($message));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->back()->with('status', 'Message sent.');
    }

    private function withinDailyLimit(int $profileId): bool
    {
        $plan = $this->getCurrentPlan();
        $limits = is_array($plan?->limits_json) ? $plan->limits_json : [];
        $dailyLimit = max(1, (int) ($limits['daily_messages'] ?? config('limits.free.daily_messages', 10)));

        $todayCount = Message::where('sender_profile_id', $profileId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return $todayCount < $dailyLimit;
    }

    private function getCurrentPlan()
    {
        $user = auth()->user();
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
