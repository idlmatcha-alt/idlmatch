@props([
    'profiles' => collect(),
    'countryHint' => null,
])

<aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between gap-2">
        <h3 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">
            @if($countryHint)
                Profiles from {{ $countryHint }}
            @else
                Suggested Profiles
            @endif
        </h3>
        <a href="{{ route('profiles.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800">
            View All
        </a>
    </div>

    <p class="mt-1 text-xs text-slate-500">
        @if($countryHint)
            Matching profiles selected using this article's country context.
        @else
            Active profiles you can explore right now.
        @endif
    </p>

    @if($profiles->isEmpty())
        <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-xs text-slate-500">
            No profiles available at the moment.
        </div>
    @else
        <div class="mt-4 space-y-3">
            @foreach($profiles as $profile)
                @php
                    $photoUrl = $profile->primaryPhoto
                        ? \App\Support\Media::thumb($profile->primaryPhoto->path, 120)
                        : asset('images/avatar-placeholder.svg');
                    $profileName = trim((string) ($profile->first_name ?: $profile->display_id));
                    $profileUrl = $profile->display_id
                        ? route('profiles.show', $profile->display_id)
                        : route('profiles.index');
                @endphp
                <a href="{{ $profileUrl }}"
                    class="group flex items-center gap-3 rounded-xl border border-slate-200 p-2.5 hover:border-brand-300 hover:bg-brand-50/20 transition">
                    <div class="h-14 w-14 rounded-full border border-brand-200 overflow-hidden bg-slate-100 shrink-0">
                        <img src="{{ $photoUrl }}" alt="{{ $profileName }}" class="h-full w-full object-cover" loading="lazy" decoding="async" width="120" height="120">
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate group-hover:text-brand-700 transition">{{ $profileName }}</p>
                        <p class="text-xs text-slate-600 truncate">{{ $profile->age ? $profile->age . ' Years' : 'Age N/A' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $profile->profession ?? 'Profession N/A' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $profile->city ?? 'N/A' }}, {{ $profile->country ?? 'N/A' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</aside>
