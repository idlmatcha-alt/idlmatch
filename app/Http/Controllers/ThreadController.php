<?php

namespace App\Http\Controllers;

use App\Models\Thread;

class ThreadController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = auth()->user();
        $user->loadMissing('profile');
        $profile = $user->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }
        $profileId = $profile->id;

        $threads = Thread::with(['profileA', 'profileB'])
            ->where(function ($q) use ($profileId) {
                $q->where('profile_a_id', $profileId)
                    ->orWhere('profile_b_id', $profileId);
            })
            ->whereHas('profileA.user', function ($q) {
                $q->nonAdmin();
            })
            ->whereHas('profileB.user', function ($q) {
                $q->nonAdmin();
            })
            ->orderByDesc('last_message_at')
            ->paginate(15);

        return view('threads.index', compact('threads', 'profileId'));
    }

    public function show(Thread $thread)
    {
        $user = auth()->user();
        $user->loadMissing('profile');
        $profile = $user->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }
        $profileId = $profile->id;

        $thread->loadMissing(['profileA.user', 'profileB.user']);
        $hasAdminParticipant = ($thread->profileA?->user?->isAdmin() ?? false)
            || ($thread->profileB?->user?->isAdmin() ?? false);
        if ($hasAdminParticipant && !$user->isAdmin()) {
            abort(404);
        }

        if (!in_array((int) $profileId, [(int) $thread->profile_a_id, (int) $thread->profile_b_id], true)) {
            abort(403);
        }

        $thread->load(['messages.sender', 'profileA', 'profileB']);

        // Mark incoming messages as read
        $thread->messages()
            ->where('sender_profile_id', '!=', $profileId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('threads.show', compact('thread', 'profileId'));
    }
}
