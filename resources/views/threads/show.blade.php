@extends('layouts.app')

@section('content')
    <div class="section max-w-4xl py-10">
        @php
            $other = $thread->profile_a_id === $profileId ? $thread->profileB : $thread->profileA;
        @endphp
        <h1 class="font-display text-3xl">Chat with {{ ($other?->first_name . ' ' . $other?->last_name) ?: 'User' }}</h1>

        <div id="messages" data-last="{{ optional($thread->messages->last())->created_at?->toDateTimeString() }}" class="mt-6 card max-h-[400px] space-y-4 overflow-y-auto p-6">
            @forelse($thread->messages as $message)
                <div class="{{ $message->sender_profile_id === $profileId ? 'text-right' : 'text-left' }}">
                    <div class="inline-block rounded-2xl px-4 py-2 text-sm {{ $message->sender_profile_id === $profileId ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                        {{ $message->body }}
                    </div>
                    <div class="mt-1 text-xs text-slate-400">{{ $message->created_at->format('d M, H:i') }}</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No messages yet.</p>
            @endforelse
        </div>

        @if(Auth::user()->canMessage())
            <form method="POST" action="{{ route('messages.store', $thread) }}" class="mt-6 flex gap-3">
                @csrf
                <input type="text" name="body" class="flex-1 field" placeholder="Type a message...">
                <button class="btn-primary">Send</button>
            </form>
        @else
            <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                <p class="text-sm text-slate-600 mb-2">Messaging is restricted on the Free plan.</p>
                <a href="{{ route('packages.index') }}" class="inline-block px-6 py-2 rounded-full font-bold text-white text-sm" style="background: linear-gradient(135deg, #FF5722, #E64A19);">
                    Upgrade to Chat Now
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('messages');
            const url = "{{ route('messages.index', $thread) }}";

            setInterval(async () => {
                const last = container.dataset.last;
                const response = await fetch(last ? `${url}?after=${encodeURIComponent(last)}` : url);
                if (!response.ok) return;
                const messages = await response.json();
                if (!messages.length) return;

                messages.forEach((message) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = message.sender_profile_id === {{ $profileId }} ? 'text-right' : 'text-left';

                    const bubble = document.createElement('div');
                    bubble.className = message.sender_profile_id === {{ $profileId }}
                        ? 'inline-block rounded-2xl px-4 py-2 text-sm bg-brand-600 text-white'
                        : 'inline-block rounded-2xl px-4 py-2 text-sm bg-slate-100 text-slate-700';
                    bubble.textContent = message.body;

                    wrapper.appendChild(bubble);
                    container.appendChild(wrapper);
                });

                container.dataset.last = messages[messages.length - 1].created_at;
            }, 5000);
        });
    </script>
@endsection
