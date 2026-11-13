@extends('layouts.app')

@section('content')
    <div class="section max-w-5xl py-10">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-500">Inbox</p>
        <h1 class="mt-3 text-3xl font-semibold">Messages</h1>
        <div class="mt-6 grid gap-4">
            @forelse($threads as $thread)
                @php
                    $other = $thread->profile_a_id === $profileId ? $thread->profileB : $thread->profileA;
                @endphp
                <a href="{{ route('threads.show', $thread) }}" class="card flex items-center justify-between p-5">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ ($other?->first_name . ' ' . $other?->last_name) ?: 'User' }}</p>
                        <p class="text-xs text-slate-500">Last activity {{ optional($thread->last_message_at)->diffForHumans() ?? '-' }}</p>
                    </div>
                    <span class="text-sm text-brand-600">Open</span>
                </a>
            @empty
                <div class="card p-6 text-sm text-slate-500">No conversations yet.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $threads->links() }}</div>
    </div>
@endsection
