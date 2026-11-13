@extends('layouts.app')

@section('header')
    <h2 class="font-display text-xl text-slate-900">
        Dashboard
    </h2>
@endsection

@section('content')
    <div class="py-10">
        <div class="section">
            <div class="mb-6 rounded-2xl border border-brand-100 bg-gradient-to-r from-brand-50 via-white to-sky-50 p-5 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600">Welcome</p>
                        <h3 class="mt-1 text-xl font-bold text-slate-900">
                            {{ $dashboardGreeting ?? 'Hello' }}, {{ $dashboardDisplayName ?? 'Member' }}
                        </h3>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-bold text-brand-700 border border-brand-200">
                        <svg class="h-4 w-4 text-brand-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Daily Message
                    </div>
                </div>
                <p class="mt-3 text-sm text-slate-700 italic">
                    "{{ $dashboardQuote }}"
                </p>
            </div>

            @if($profile)
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('profiles.show', $profile->display_id) }}" class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-white px-4 py-2 text-xs font-semibold text-brand-700 shadow-sm hover:border-brand-300 hover:text-brand-800">
                        View My Profile
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            @endif
            <div class="grid gap-6 lg:grid-cols-4">
                {{-- Package/Plan Card --}}
                <div class="card p-6" style="background: linear-gradient(135deg, #881337, #C2185B);">
                    @php
                        $subscription = auth()->user()->getActiveSubscription();
                        $planPrice = $subscription ? $subscription->plan->price : 0;
                        $isFree = !$subscription || $planPrice == 0;
                        
                        $latestSubscription = $latestSubscription ?? null;
                        $isPending = $latestSubscription && $latestSubscription->status === 'pending';
                        
                        if ($isPending && $isFree) {
                             $planName = $latestSubscription->plan->name . ' (Pending)';
                             $planPrice = $latestSubscription->plan->price;
                        } elseif ($subscription) {
                            $planName = $subscription->plan->name;
                            $planPrice = $subscription->plan->price;
                        } else {
                            $planName = 'No Plan';
                            $planPrice = 0;
                        }
                    @endphp
                    <p class="text-xs uppercase text-brand-100">Current Package</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ $planName }}</p>
                    
                    @if($subscription && $subscription->end_at && !($isPending && $isFree))
                        <p class="mt-1 text-xs text-brand-100">Expires: {{ $subscription->end_at->format('M d, Y') }}</p>
                    @elseif($isPending && $isFree)
                         <p class="mt-1 text-xs font-bold text-yellow-300 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verification Pending
                        </p>
                    @endif

                    {{-- Package Details --}}
                    @if(($subscription ?? $latestSubscription) && ($subscription ?? $latestSubscription)->plan->limits_json)
                    
                        <div class="mt-4 pt-4 border-t border-white/10 space-y-2">
                            @php 
                                $limits = ($isPending && $isFree) ? $latestSubscription->plan->limits_json : ($subscription ? $subscription->plan->limits_json : []); 
                            @endphp
                            <div class="flex items-center justify-between text-xs text-brand-100">
                                <span>Profile Views</span>
                                <span class="font-bold">{{ $limits['profile_views'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-brand-100">
                                <span>Contact Access</span>
                                <span class="font-bold">{{ ($limits['contact_access'] ?? false) ? 'Yes' : 'No' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-brand-100">
                                <span>Verified Badge</span>
                                <span class="font-bold">{{ ($limits['verified_badge'] ?? false) ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                    @endif

                    @if($isFree && !$isPending)
                        <a href="{{ route('packages.index') }}" class="mt-4 inline-flex items-center gap-1 px-4 py-2 rounded-lg text-sm font-bold transition bg-white text-brand-700 hover:bg-brand-50 shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Upgrade Now
                        </a>
                    @elseif($isPending)
                        <div class="mt-4 p-3 bg-white/10 rounded-lg text-xs text-white">
                            Your payment proof is under review. Access will be granted shortly.
                        </div>
                    @else
                        <a href="{{ route('packages.index') }}" class="mt-4 inline-flex text-sm font-semibold text-brand-200 hover:text-white">View Plans →</a>
                    @endif
                </div>
                <div class="card p-6" x-data="{ completionModalOpen: false }">
                    <p class="text-xs uppercase text-slate-400">Profile completion</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $profile->completion_score }}%</p>
                    <div class="mt-3 h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all" style="width: {{ $profile->completion_score }}%"></div>
                    </div>
                    @if($profile->completion_score < 100)
                        @if(isset($prompts) && count($prompts) > 0)
                            <div class="mt-4 space-y-1">
                                @foreach(array_slice($prompts, 0, 3) as $prompt)
                                    <p class="text-xs text-slate-500 flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        {{ $prompt }}
                                    </p>
                                @endforeach
                                @if(count($prompts) > 3)
                                    <p class="text-xs text-slate-400 pl-4.5">+ {{ count($prompts) - 3 }} more</p>
                                @endif
                            </div>
                        @endif
                        <button type="button"
                            @click="completionModalOpen = true"
                            class="mt-4 block w-full py-2 px-4 bg-brand-600 hover:bg-brand-700 text-white text-center rounded-lg text-sm font-bold transition shadow-sm hover:shadow">
                            Complete Profile
                        </button>

                        <div x-show="completionModalOpen" x-cloak x-transition.opacity
                            class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                            @click.self="completionModalOpen = false"
                            @keydown.escape.window="completionModalOpen = false">
                            @php
                                $pendingCollection = collect($pendingItems ?? []);
                                $editablePendingItems = $pendingCollection->where('editable', true)->values();
                            @endphp
                            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900">Pending Profile Fields</h3>
                                        <p class="mt-1 text-xs text-slate-500">Update these pending fields directly here.</p>
                                    </div>
                                    <button type="button" @click="completionModalOpen = false"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50"
                                        aria-label="Close pending fields popup">
                                        <span aria-hidden="true" class="text-lg leading-none">&times;</span>
                                    </button>
                                </div>

                                @if($pendingCollection->isNotEmpty())
                                    <form action="{{ route('profile.complete.store') }}" method="POST" class="mt-4">
                                        @csrf
                                        <div class="max-h-72 overflow-y-auto rounded-xl border border-slate-100 bg-slate-50 p-3 space-y-3">
                                            @foreach($pendingCollection as $item)
                                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <label class="text-xs font-bold uppercase tracking-wide text-slate-600">
                                                            {{ $item['label'] ?? 'Pending Field' }}
                                                        </label>
                                                        @if(!($item['editable'] ?? false))
                                                            @php $actionRoute = $item['action_route'] ?? 'profile.complete-setup'; @endphp
                                                            <a href="{{ route($actionRoute) }}"
                                                                class="text-[11px] font-bold text-brand-700 hover:text-brand-800">
                                                                Edit
                                                            </a>
                                                        @endif
                                                    </div>

                                                    @if($item['editable'] ?? false)
                                                        @if(($item['type'] ?? '') === 'textarea')
                                                            <textarea
                                                                name="{{ $item['key'] }}"
                                                                rows="3"
                                                                class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-brand-500"
                                                                placeholder="{{ $item['prompt'] ?? '' }}">{{ old($item['key'], $item['value'] ?? '') }}</textarea>
                                                        @elseif(($item['type'] ?? '') === 'select')
                                                            <select
                                                                name="{{ $item['key'] }}"
                                                                class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-brand-500">
                                                                <option value="">Select {{ strtolower($item['label'] ?? 'option') }}</option>
                                                                @foreach(($item['options'] ?? []) as $optionValue => $optionLabel)
                                                                    <option value="{{ $optionValue }}" @selected(old($item['key'], $item['value'] ?? '') == $optionValue)>{{ $optionLabel }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <input
                                                                type="{{ $item['type'] ?? 'text' }}"
                                                                name="{{ $item['key'] }}"
                                                                value="{{ old($item['key'], $item['value'] ?? '') }}"
                                                                class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-brand-500"
                                                                placeholder="{{ $item['prompt'] ?? '' }}">
                                                        @endif
                                                    @else
                                                        <p class="mt-2 text-sm text-slate-600">{{ $item['prompt'] ?? 'Update this item to continue.' }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                                            <button type="button" @click="completionModalOpen = false"
                                                class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                                Close
                                            </button>
                                            @if($editablePendingItems->isNotEmpty())
                                                <button type="submit"
                                                    class="px-4 py-2 rounded-lg bg-brand-600 text-xs font-bold text-white hover:bg-brand-700 transition">
                                                    Save Pending Fields
                                                </button>
                                            @endif
                                            <a href="{{ route('profile.complete-setup') }}"
                                                class="px-4 py-2 rounded-lg bg-slate-700 text-xs font-bold text-white hover:bg-slate-800 transition">
                                                Open Full Editor
                                            </a>
                                        </div>
                                    </form>
                                @else
                                    <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-3">
                                        <p class="text-sm text-slate-600">No pending fields found.</p>
                                    </div>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" @click="completionModalOpen = false"
                                            class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                            Close
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-green-600 font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Profile Complete!
                        </p>
                    @endif
                </div>
                <div class="card p-6 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 bg-brand-50 text-brand-500 rounded-full p-2 group-hover:bg-brand-500 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <p class="text-xs uppercase text-slate-400">Profile Views</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ auth()->user()->getUsedProfileViewsCount() }} <span class="text-sm font-normal text-slate-400">/ {{ auth()->user()->getProfileViewLimit() }}</span>
                    </p>
                    <div class="mt-4 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                        @php
                            $used = auth()->user()->getUsedProfileViewsCount();
                            $limit = auth()->user()->getProfileViewLimit();
                            $percentage = $limit > 0 ? min(100, ($used / $limit) * 100) : 0;
                        @endphp
                        <div class="h-full bg-brand-500 transition-all" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                <div class="card p-6 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 bg-pink-50 text-pink-500 rounded-full p-2 group-hover:bg-pink-500 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <p class="text-xs uppercase text-slate-400">Interests received</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $interestsReceived->count() }}</p>
                </div>
                <div class="card p-6 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 bg-amber-50 text-amber-500 rounded-full p-2 group-hover:bg-amber-400 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <p class="text-xs uppercase text-slate-400">Favorites</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $myFavorites->count() }}</p>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                <div class="card p-6">
                    <h3 class="text-lg font-semibold">Latest interests</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse($interestsReceived as $interest)
                            <div class="flex flex-col gap-2 rounded-2xl border border-slate-100/70 bg-white/80 p-4">
                                <div class="flex items-center justify-between">
                                    <span>{{ $interest->fromProfile?->first_name ?: 'User' }}</span>
                                    <span class="text-xs uppercase text-slate-400">{{ $interest->status }}</span>
                                </div>
                                @if($interest->status === 'pending')
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('interests.accept', $interest) }}">
                                            @csrf
                                            <button class="btn-primary px-4 py-1.5 text-xs">Accept</button>
                                        </form>
                                        <form method="POST" action="{{ route('interests.decline', $interest) }}">
                                            @csrf
                                            <button class="btn-secondary px-4 py-1.5 text-xs">Decline</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p>No interests yet.</p>
                        @endforelse
                    </div>
                </div>
                <div class="card p-6">
                    <h3 class="text-lg font-semibold">Recent chats</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse($threads as $thread)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-100/70 bg-white/80 p-4">
                                <span>Thread #{{ $thread->id }}</span>
                                <a href="{{ route('threads.show', $thread) }}" class="text-brand-600">Open</a>
                            </div>
                        @empty
                            <p>No chats yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-900 border-l-4 border-amber-500 pl-3">My Favorites</h3>
                    <a href="{{ route('favorites.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View All →</a>
                </div>
                <div class="mt-6 space-y-4">
                    @forelse($myFavorites as $favoriteProfile)
                        <x-profile-card :profile="$favoriteProfile" />
                    @empty
                        <div class="py-10 text-center bg-white rounded-xl border border-slate-100 shadow-sm">
                            <div class="mx-auto w-12 h-12 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-slate-500">You haven't favorited anyone yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-900 border-l-4 border-brand-500 pl-3">New Matches for You</h3>
                    <a href="{{ route('profiles.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View All →</a>
                </div>
                <div class="mt-6 space-y-4">
                    @forelse($matches as $match)
                        <x-profile-card :profile="$match" />
                    @empty
                        <p class="col-span-full py-10 text-center text-slate-500 card">No matches found for your profile yet.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
