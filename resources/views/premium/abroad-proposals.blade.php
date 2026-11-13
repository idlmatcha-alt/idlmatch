@extends('layouts.marketing')


@section('content')
    <section class="pt-8 pb-12 min-h-screen" style="background-color: #f8f7fc;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            {{-- Hero Section --}}
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold mb-3" style="color: #E91E63;">🌍 Abroad Proposals</h1>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Exclusive profiles of premium members with {{ $planName }}. 
                    Connect with verified individuals ready for international matrimonial alliances.
                </p>

                @php
                    $couplePhotos = \Illuminate\Support\Facades\Cache::remember('contact_couple_photos_v1', 3600, function () {
                        return collect(\Illuminate\Support\Facades\Storage::disk('public')->files('couple_photos'))
                            ->filter(fn ($file) => preg_match('/\.(jpe?g|png|webp)$/i', $file))
                            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                            ->values()
                            ->all();
                    });
                @endphp

                @if(count($couplePhotos))
                    <style>
                        .contact-couple-marquee {
                            overflow: hidden;
                            -webkit-mask-image: linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
                            mask-image: linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
                        }
                        .contact-couple-track {
                            display: flex;
                            gap: 1rem;
                            width: max-content;
                            animation: contact-couple-scroll {{ max(60, count($couplePhotos) * 3) }}s linear infinite;
                            will-change: transform;
                        }
                        .contact-couple-marquee:hover .contact-couple-track { animation-play-state: paused; }
                        .contact-couple-item { flex: 0 0 auto; width: clamp(9rem, 30vw, 14rem); }
                        @keyframes contact-couple-scroll {
                            from { transform: translateX(0); }
                            to { transform: translateX(calc(-50% - 0.5rem)); }
                        }
                        @media (prefers-reduced-motion: reduce) {
                            .contact-couple-track { animation: none; }
                        }
                    </style>

                    <div class="contact-couple-marquee mt-8 rounded-2xl mx-auto max-w-4xl"
                        aria-label="Couples who found their match with Ideal Marriage Center">
                        <div class="contact-couple-track">
                            @foreach([false, true] as $isClone)
                                @foreach($couplePhotos as $photo)
                                    @php
                                        $photoLabel = trim(preg_replace(
                                            '/[_-]+/',
                                            ' ',
                                            preg_replace('/^\d+[_-]/', '', pathinfo(basename($photo), PATHINFO_FILENAME))
                                        ));
                                    @endphp
                                    <div class="contact-couple-item overflow-hidden rounded-xl bg-slate-100"
                                        style="aspect-ratio: 4 / 5;"
                                        @if($isClone) aria-hidden="true" @endif>
                                        <img src="{{ \App\Support\Media::thumb($photo, 320) }}"
                                            alt="{{ $isClone ? '' : 'Happy couple — ' . $photoLabel }}"
                                            class="block w-full h-full object-cover"
                                            loading="lazy" decoding="async"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 text-white shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-semibold">Premium Members Only</span>
                </div>
            </div>

            {{-- Profile Cards --}}
            <div class="space-y-4">
                @forelse($profiles as $profile)
                    <x-profile-card :profile="$profile" />
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <h3 class="mt-4 text-xl font-bold text-slate-900">No Abroad Proposals Available</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            Currently, there are no members with {{ $planName }}. Check back soon for exclusive international profiles!
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('profiles.index') }}" 
                                class="inline-block px-6 py-3 rounded-lg text-sm font-bold text-white transition-colors"
                                style="background: linear-gradient(135deg, #E91E63, #C2185B);">
                                Browse All Profiles
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($profiles->hasPages())
                <div class="mt-8">
                    {{ $profiles->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
