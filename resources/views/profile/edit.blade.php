<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl text-slate-900 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-privacy-form')
                </div>
            </div>

            <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                <div class="space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">Your Profile Details</h3>
                            <p class="mt-1 text-sm text-slate-500">You can view all saved profile data here and update it anytime.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('profile.complete-setup') }}"
                                class="inline-flex items-center rounded-lg bg-brand-600 px-3 py-2 text-xs font-bold text-white hover:bg-brand-700 transition">
                                Edit Complete Profile
                            </a>
                            <a href="{{ route('profile.preferences') }}"
                                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                Edit Preferences
                            </a>
                        </div>
                    </div>

                    @php
                        $profile = $user->profile;
                    @endphp

                    @if($profile)
                        @php
                            $profileFields = array_values(array_unique(array_merge($profile->getFillable(), ['created_at', 'updated_at'])));
                        @endphp
                        <div class="overflow-auto max-h-96 rounded-xl border border-slate-200">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold text-slate-600 uppercase text-[11px] tracking-wide">Field</th>
                                        <th class="px-3 py-2 text-left font-semibold text-slate-600 uppercase text-[11px] tracking-wide">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($profileFields as $field)
                                        @php
                                            $value = data_get($profile, $field);
                                            if ($value instanceof \DateTimeInterface) {
                                                $displayValue = $value->format('Y-m-d H:i:s');
                                            } elseif (is_array($value)) {
                                                $flat = collect($value)->flatten()->filter(fn ($item) => $item !== null && $item !== '')->values();
                                                $displayValue = $flat->isEmpty() ? '-' : $flat->implode(', ');
                                            } elseif (is_bool($value)) {
                                                $displayValue = $value ? 'Yes' : 'No';
                                            } else {
                                                $stringValue = trim((string) $value);
                                                $displayValue = $stringValue !== '' ? $stringValue : '-';
                                            }
                                        @endphp
                                        <tr class="border-t border-slate-100">
                                            <td class="px-3 py-2 text-slate-700 font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $displayValue }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @php
                            $preferences = $profile->preferences;
                        @endphp
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800 mb-2">Partner Preferences</h4>
                            @if($preferences)
                                @php
                                    $preferenceFields = array_values(array_unique(array_merge($preferences->getFillable(), ['created_at', 'updated_at'])));
                                @endphp
                                <div class="overflow-auto max-h-80 rounded-xl border border-slate-200">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-slate-50 sticky top-0">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-semibold text-slate-600 uppercase text-[11px] tracking-wide">Field</th>
                                                <th class="px-3 py-2 text-left font-semibold text-slate-600 uppercase text-[11px] tracking-wide">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($preferenceFields as $field)
                                                @php
                                                    $value = data_get($preferences, $field);
                                                    if ($value instanceof \DateTimeInterface) {
                                                        $displayValue = $value->format('Y-m-d H:i:s');
                                                    } elseif (is_array($value)) {
                                                        $flat = collect($value)->flatten()->filter(fn ($item) => $item !== null && $item !== '')->values();
                                                        $displayValue = $flat->isEmpty() ? '-' : $flat->implode(', ');
                                                    } elseif (is_bool($value)) {
                                                        $displayValue = $value ? 'Yes' : 'No';
                                                    } else {
                                                        $stringValue = trim((string) $value);
                                                        $displayValue = $stringValue !== '' ? $stringValue : '-';
                                                    }
                                                @endphp
                                                <tr class="border-t border-slate-100">
                                                    <td class="px-3 py-2 text-slate-700 font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}</td>
                                                    <td class="px-3 py-2 text-slate-600">{{ $displayValue }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-slate-500">No preferences saved yet.</p>
                            @endif
                        </div>
                    @else
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm text-amber-700">No profile found yet. Click <strong>Edit Complete Profile</strong> to add your details.</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($profile)
                <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-bold text-slate-900 font-display">
                                Blocked Profiles
                            </h2>
                            <p class="mt-1 text-sm text-slate-600">
                                Profiles you have blocked will not appear in your search results and cannot interact with you.
                            </p>
                        </header>
                        
                        @php
                            $blockedDocs = \App\Models\Block::with('blockedProfile.user')
                                ->where('blocker_profile_id', $profile->id)
                                ->get();
                        @endphp
                        
                        <div class="mt-6">
                            @if($blockedDocs->isEmpty())
                                <p class="text-sm text-slate-500 italic">You have no blocked profiles.</p>
                            @else
                                <ul class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
                                    @foreach($blockedDocs as $block)
                                        <li class="p-4 flex items-center justify-between bg-slate-50 hover:bg-slate-100 transition">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden shrink-0">
                                                    @if($block->blockedProfile && $block->blockedProfile->primary_photo_url)
                                                        <img src="{{ $block->blockedProfile->primary_photo_url }}" class="w-full h-full object-cover">
                                                    @else
                                                        <svg class="w-full h-full text-slate-400 p-2" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900">
                                                        {{ $block->blockedProfile->first_name ?? 'Unknown User' }} 
                                                        <span class="text-slate-500 text-xs font-normal">({{ $block->blockedProfile->display_id ?? 'N/A' }})</span>
                                                    </p>
                                                    <p class="text-xs text-slate-500">Blocked on {{ $block->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            @if($block->blockedProfile)
                                                <form method="POST" action="{{ route('blocks.destroy', $block->blockedProfile->display_id) }}" onsubmit="return confirm('Are you sure you want to unblock this profile?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 transition px-3 py-1.5 rounded bg-red-50 hover:bg-red-100">
                                                        Unblock
                                                    </button>
                                                </form>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
