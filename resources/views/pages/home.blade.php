@extends('layouts.marketing')

@section('content')
    <!-- Hero Section (New Shaadi.com Style) -->
    <section class="relative flex items-center justify-center bg-gray-900 -mt-6"
        style="min-height: calc(70vh - 120px); height: 780px;">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/heroimg.jpg') }}"
                class="w-full h-full object-cover object-center" alt="Bride and groom seated at a decorated wedding venue arranged through Ideal Marriage Center" loading="eager" fetchpriority="high" decoding="async" width="1536" height="1024">
            <!-- Blue Glow Vignette (Shaadi Style) -->
            <div class="absolute inset-0 bg-gradient-to-b from-[#0077b6]/40 via-transparent to-[#0077b6]/40"></div>
            <div class="absolute inset-0 bg-black/30"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 w-full max-w-3xl px-4 text-center flex flex-col items-center justify-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight drop-shadow-lg font-sans">
                Find your forever <span class="text-red-400">❤️</span>
            </h1>
            <p class="mt-4 text-lg md:text-xl text-white/80 font-light tracking-wide">
                Discover a world beyond matrimony
            </p>
            
            <!-- Single CTA Button -->
            <a href="{{ route('register') }}" class="mt-10 inline-block bg-cta hover:bg-cta-hover text-white font-bold text-lg px-12 py-4 rounded-full shadow-2xl transition transform hover:-translate-y-1 hover:shadow-[0_10px_40px_rgba(10,164,184,0.4)]">
                Find Your Match
            </a>
        </div>

        <!-- Stats Bar (Overlay at Bottom of Hero) -->
        <div class="absolute bottom-0 left-0 right-0 z-20 bg-gradient-to-t from-black/70 to-transparent py-5">
            <div class="max-w-4xl mx-auto px-4 flex flex-wrap justify-center items-center gap-x-6 gap-y-2 text-white/90 text-xs font-medium">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    #1 Matchmaking Service
                </span>
                <span class="hidden md:inline text-white/40">|</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    Ratings on Playstore by 2.4 Lakh users
                </span>
                <span class="hidden md:inline text-white/40">|</span>
                <span>80 Lakh Success Stories</span>
            </div>
        </div>
    </section>


    <!-- The Ideal Marriage Center Experience Section (Shaadi Match) -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">The Ideal Marriage Center Experience</h2>
            
            <!-- Three Feature Cards -->
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1: Money Back Guarantee -->
                <div class="bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 bg-[#0aa4b8]/10 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-[#0aa4b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3 text-center">30 Day Money Back Guarantee</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Get matched with someone special within 30 days, or we'll refund your money—guaranteed!
                    </p>
                </div>

                <!-- Card 2: Verified Profiles -->
                <div class="bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 bg-[#0aa4b8]/10 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-[#0aa4b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3 text-center">Verified Badge to Find Your Match</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Did you know our verified profiles get 40% more connection requests than others?
                    </p>
                </div>

                <!-- Card 3: AI-Powered Matchmaking -->
                <div class="bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 bg-[#0aa4b8]/10 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-[#0aa4b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3 text-center">Matchmaking Powered by AI</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Cutting-edge technology with years of matchmaking expertise to help you find 'the one'.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- VIP Ideal Marriage Center Section (Shaadi Premium Style) -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#EFEBFA] rounded-[32px] p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8 md:gap-16">
                <!-- Branding Column -->
                <div class="flex flex-col items-center md:items-start text-center md:text-left gap-4 flex-shrink-0">
                    <div class="flex flex-col items-center md:items-start">
                        <!-- Geometric VIP Logo -->
                        <div class="mb-4">
                            <svg width="48" height="48" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-[#3D2177]">
                                <path d="M20 30L35 10H5L20 30Z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M20 25L30 11.5H10L20 25Z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M20 20L25 13H15L20 20Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#3D2177] tracking-[0.2em] uppercase">VIP IDEAL MARRIAGE CENTER</h3>
                        <p class="text-[10px] text-[#3D2177] font-bold tracking-[0.3em] mt-1">NO.1 MATCHMAKING SERVICE FOR ELITES</p>
                    </div>
                </div>

                <!-- Content & CTA Column -->
                <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12 flex-grow">
                    <div class="text-center md:text-left">
                        <p class="text-xl md:text-2xl font-medium text-[#3D2177] leading-tight">
                            Experience the world of elite personalised matchmaking by Ideal Marriage Center
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <style>
                            .vip-btn-premium { 
                                background-color: #48276E !important; 
                                white-space: nowrap !important;
                                display: inline-flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                min-width: 220px;
                            }
                            .vip-btn-premium:hover { background-color: #3D215E !important; }
                        </style>
                        <a href="{{ route('contact') }}" class="vip-btn-premium text-white font-bold px-10 py-4 rounded-full transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Free Consultation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Founder's Quote / Trust Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="relative">
                <svg class="absolute top-0 left-0 w-12 h-12 text-[#0aa4b8]/20 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
                <blockquote class="text-2xl md:text-3xl font-serif text-gray-800 leading-relaxed italic px-12">
                    "Every successful match on Ideal Marriage Center is a testament to our commitment to helping people find genuine connections and lasting love."
                </blockquote>
                <p class="mt-6 text-sm font-semibold text-gray-600">— The Ideal Marriage Center Team</p>
            </div>
        </div>
    </section>

    <!-- Featured Profiles Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-2">Featured Profiles</h2>
            <p class="text-center text-gray-500 mb-12">Discover potential matches just for you</p>
            
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($profiles as $profile)
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 group overflow-hidden flex flex-col">
                        <!-- Top Section: Image & Basic Info -->
                        <div class="flex h-56">
                            <div class="w-1/2 relative bg-slate-200">
                                @if($profile->primaryPhoto)
                                    <img src="{{ \App\Support\Media::thumb($profile->primaryPhoto->path, 400) }}" alt="Verified {{ $profile->gender === 'male' ? 'groom' : 'bride' }} biodata {{ $profile->display_id }} on Ideal Marriage Center" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" loading="lazy" decoding="async">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    </div>
                                @endif
                                
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                                @if($profile->user?->activeSubscription)
                                    <div class="absolute top-0 left-0 z-10">
                                        <div class="relative">
                                            <div class="bg-gradient-to-r from-amber-400 to-amber-600 text-white text-[10px] font-bold uppercase py-1 px-3 shadow-md rounded-br-lg border-b border-r border-amber-700/20">
                                                {{ $profile->user->activeSubscription->plan->name ?? 'Premium' }}
                                            </div>
                                        </div>
                                    </div>
                                @endif



                                <!-- Match Badge -->
                                @if(isset($profile->match_percentage))
                                    <div class="absolute top-2 right-2 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-lg flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                                        {{ $profile->match_percentage }}%
                                    </div>
                                @endif

                                <div class="absolute bottom-3 left-3 text-white">
                                    <p class="text-sm font-bold">{{ $profile->age }} Yrs, {{ $profile->height_cm }} cm</p>
                                </div>
                            </div>
                            
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-lg font-bold text-slate-900 line-clamp-1">
                                            {{ $profile->first_name ?: ($profile->user->first_name ?? $profile->user->name ?? ($profile->gender == 'male' ? 'Male Profile' : 'Female Profile')) }}
                                        </h3>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 mt-1">{{ $profile->gender == 'male' ? 'Male' : 'Female' }}</p>
                                    
                                    <div class="mt-3 space-y-1.5">
                                        <div class="flex items-center gap-2 text-xs text-slate-600">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            <span class="truncate">{{ $profile->profession }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-slate-600">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            <span class="truncate">{{ $profile->city }}, {{ $profile->country }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-slate-600">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8M12 3v18"></path></svg>
                                            <span class="truncate">{{ $profile->religion }}, {{ $profile->caste }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons (Bottom Band) -->
                        <div class="bg-slate-50 border-t border-slate-100 p-3 flex gap-3">
                            <a href="{{ route('profiles.show', $profile->display_id) }}" class="flex-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-bold py-2.5 flex items-center justify-center hover:bg-slate-50 hover:text-brand-600 transition">
                                View Profile
                            </a>
                            @auth
                                <form method="POST" action="{{ route('interests.send') }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="to_display_id" value="{{ $profile->display_id }}">
                                    <button class="w-full rounded-lg bg-brand-600 text-white text-xs font-bold py-2.5 flex items-center justify-center hover:bg-brand-700 transition shadow-sm">
                                        Connect
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="flex-1 rounded-lg bg-brand-600 text-white text-xs font-bold py-2.5 flex items-center justify-center hover:bg-brand-700 transition shadow-sm">
                                    Connect
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('profiles.index') }}" class="inline-flex items-center gap-2 text-brand-600 font-bold hover:text-brand-700">
                    Browse All Profiles
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Matches Across Borders -->
    <section id="matches-across-borders" class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-900">Matches Across Borders</h2>
                <p class="text-slate-500 mt-2 max-w-2xl mx-auto">Couples from Pakistan, India, Russia, Thailand global world who found their life partner with Ideal Marriage Center.</p>
            </div>

            @php
                $borderCouples = [
                    ['image' => 'countries/pk-green.jpg', 'country' => 'Pakistan'],
                    ['image' => 'countries/in-red.jpg', 'country' => 'India'],
                    ['image' => 'countries/ru-city.jpg', 'country' => 'Russia'],
                    ['image' => 'countries/th-gold.jpg', 'country' => 'Thailand'],
                ];
            @endphp

            <style>
                .idm-marquee {
                    overflow: hidden;
                    -webkit-mask-image: linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
                    mask-image: linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
                }
                .idm-marquee-track {
                    display: flex;
                    gap: 1rem;
                    width: max-content;
                    animation: idm-marquee-scroll 26s linear infinite;
                    will-change: transform;
                }
                .idm-marquee:hover .idm-marquee-track { animation-play-state: paused; }
                .idm-marquee-item { flex: 0 0 auto; width: 17.5rem; max-width: 72vw; }
                @keyframes idm-marquee-scroll {
                    from { transform: translateX(0); }
                    to { transform: translateX(calc(-50% - 0.5rem)); }
                }
                @media (prefers-reduced-motion: reduce) {
                    .idm-marquee-track { animation: none; }
                }
            </style>

            <div class="idm-marquee" aria-label="Matches across borders">
                <div class="idm-marquee-track">
                    @foreach([false, true] as $isClone)
                        @foreach($borderCouples as $couple)
                            <a href="{{ route('success-stories.index') }}"
                                class="idm-marquee-item relative block overflow-hidden rounded-xl bg-slate-200 group"
                                style="aspect-ratio: 4 / 5;"
                                aria-label="Success story from {{ $couple['country'] }}"
                                @if($isClone) aria-hidden="true" tabindex="-1" @endif>
                                <img src="{{ Storage::url($couple['image']) }}"
                                    alt="Happy couple from {{ $couple['country'] }}"
                                    class="block w-full h-full object-cover group-hover:scale-105 transition duration-700"
                                    loading="lazy" decoding="async"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-4 pt-8 pb-3">
                                    <span class="text-white text-sm font-bold">{{ $couple['country'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('success-stories.index') }}"
                    class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-bold text-white bg-brand-700 hover:bg-brand-800 transition">
                    Read Success Stories
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Blog Articles -->
    <section class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Featured Blog Articles</h2>
                    <p class="text-slate-500 mt-1">Randomly selected marriage guidance, tips, and relationship insights</p>
                </div>
                <a href="{{ route('blog.index') }}"
                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold text-white bg-brand-700 hover:bg-brand-800 transition whitespace-nowrap">
                    View All Blogs
                </a>
            </div>

            @if($posts->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-600">
                    New blog articles will be published soon.
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach($posts as $post)
                        @php
                            $postImage = $post->featured_image ?: $post->cover_image;
                            $postExcerpt = trim((string) $post->excerpt);
                        @endphp
                        <article class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition flex flex-col h-full">
                            <a href="{{ route('blog.show', $post) }}" class="block">
                                <div class="bg-slate-100 overflow-hidden" style="aspect-ratio: 16 / 10; min-height: 12rem;">
                                    @if($postImage)
                                        <img src="{{ \App\Support\Media::thumb($postImage, 640) }}"
                                            alt="{{ $post->title }} — marriage advice article by Ideal Marriage Center"
                                            class="block"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            loading="lazy"
                                            decoding="async"
                                            width="640" height="400">
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 text-sm font-semibold">
                                            Blog Article
                                        </div>
                                    @endif
                                </div>
                            </a>

                            <div class="p-5 flex flex-col grow">
                                <p class="text-xs uppercase tracking-wide text-slate-400">
                                    {{ optional($post->published_at)->format('M d, Y') ?? $post->created_at->format('M d, Y') }}
                                </p>
                                <h3 class="mt-2 text-lg font-bold text-slate-900 leading-snug">
                                    <a href="{{ route('blog.show', $post) }}" class="hover:text-brand-700 transition">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="mt-2 text-sm text-slate-600">
                                    {{ $postExcerpt !== '' ? \Illuminate\Support\Str::limit($postExcerpt, 120) : 'Read the full article for helpful insights and guidance.' }}
                                </p>
                                <a href="{{ route('blog.show', $post) }}"
                                    class="inline-flex items-center gap-1 text-sm font-bold text-brand-700 hover:text-brand-800 mt-auto pt-4">
                                    Read More
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Success Stories Section (Moved Below) -->
    <section class="section py-20 bg-gray-50">
        <h2 class="text-3xl font-display font-bold text-center text-brand-900 mb-2">Matrimony Service with Millions of Success Stories</h2>
        <p class="text-center text-gray-500 mb-12">Randomly selected from approved stories shared by real members</p>
        
        <div class="grid md:grid-cols-3 gap-6">
            @forelse($stories as $story)
                <a href="{{ route('success-stories.show', $story) }}"
                    class="block bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden group"
                    aria-label="Read story of {{ $story->couple_names }}">
                    <div class="h-64 overflow-hidden relative">
                         @if($story->cover_image)
                            <img src="{{ \App\Support\Media::thumb($story->cover_image, 640) }}" alt="{{ $story->couple_names ?: 'IDL Match' }} marriage success story" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" decoding="async" width="640" height="640">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <span class="text-4xl text-brand-200">❤</span>
                            </div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="font-bold text-xl">{{ $story->couple_names }}</h3>
                            <div class="h-px w-12 bg-brand-500 my-2"></div>
                            <p class="text-sm opacity-90 line-clamp-2">{{ $story->story }}</p>
                        </div>
                    </div>
                </a>
                            @empty
                <!-- Fallback Story Cards if DB empty -->
                <!-- Card 1: Saad & Rabia -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                    <div class="h-64 overflow-hidden relative">
                        <img src="{{ asset('images/couples/saad_rabia.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" decoding="async" width="760" height="760" alt="Saad and Rabia, a couple married through Ideal Marriage Center in Rawalpindi">
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="font-bold text-xl">â¤ Saad &amp; Rabia</h3>
                            <div class="h-px w-12 bg-pink-500 my-2"></div>
                            <p class="text-sm opacity-90 line-clamp-2">We met through Ideal Marriage Center and found a strong connection based on values and family goals in Rawalpindi.</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-gray-400 font-mono">Ref: ZZAI</p>
                    </div>
                </div>

                <!-- Card 2: Talha & Hira -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                    <div class="h-64 overflow-hidden relative">
                        <img src="{{ asset('images/couples/talha_hira.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" decoding="async" width="760" height="760" alt="Talha and Hira, a couple married through Ideal Marriage Center in Karachi">
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="font-bold text-xl">â¤ Talha &amp; Hira</h3>
                            <div class="h-px w-12 bg-pink-500 my-2"></div>
                            <p class="text-sm opacity-90 line-clamp-2">We met through Ideal Marriage Center and found a strong connection based on values and family goals in Karachi.</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-gray-400 font-mono">Ref: CGKO</p>
                    </div>
                </div>

                <!-- Card 3: Zain & Maham -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                    <div class="h-64 overflow-hidden relative">
                        <img src="{{ asset('images/couples/zain_maham.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" decoding="async" width="760" height="760" alt="Zain and Maham, a couple married through Ideal Marriage Center in Multan">
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="font-bold text-xl">â¤ Zain &amp; Maham</h3>
                            <div class="h-px w-12 bg-pink-500 my-2"></div>
                            <p class="text-sm opacity-90 line-clamp-2">We met through Ideal Marriage Center and found a strong connection based on values and family goals in Multan.</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-gray-400 font-mono">Ref: BOOC</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('success-stories.index') }}" class="text-brand-600 font-bold hover:text-brand-700 underline underline-offset-4">View All Success Stories</a>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">Frequently Asked Questions</h2>
            
            <div x-data="{ openFaq: null }" class="space-y-4">
                <!-- FAQ 1 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">How to register on Ideal Marriage Center?</span>
                        <svg class="w-5 h-5 transition-transform text-gray-600" :class="{'rotate-180': openFaq === 1}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Simply click on "Find Your Match" or "Register Free" buttons, fill in your basic details, verify your email, and start your journey to find your perfect match. The registration process is quick and secure.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">Is my data safe and secure?</span>
                        <svg class="w-5 h-5 transition-transform text-gray-600" :class="{'rotate-180': openFaq === 2}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Absolutely! We use industry-standard encryption and security measures to protect your personal information. Your privacy is our top priority, and we never share your data with third parties without your consent.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">What makes Ideal Marriage Center different from other matrimonial sites?</span>
                        <svg class="w-5 h-5 transition-transform text-gray-600" :class="{'rotate-180': openFaq === 3}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Ideal Marriage Center combines advanced AI-powered matchmaking with verified profiles, ensuring authentic connections. We offer personalized match recommendations, 24/7 customer support, and a 30-day money-back guarantee for premium members.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">How does the verified badge work?</span>
                        <svg class="w-5 h-5 transition-transform text-gray-600" :class="{'rotate-180': openFaq === 4}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Our verification process includes identity verification, photo verification, and contact details verification. Verified profiles receive a special badge and get 40% more connection requests, ensuring trust and authenticity.
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">What are the membership benefits?</span>
                        <svg class="w-5 h-5 transition-transform text-gray-600" :class="{'rotate-180': openFaq === 5}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Premium members get unlimited messaging, advanced search filters, profile highlighting, priority customer support, and access to exclusive VIP matchmaking services. Plus, enjoy our 30-day money-back guarantee with full confidence.
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">How long does it typically take to find a match?</span>
                        <svg class="w-5 h-5 transition-transform text-gray-600" :class="{'rotate-180': openFaq === 6}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 6" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        While every journey is unique, our AI-powered matching system and vast database of verified profiles help most users find compatible matches within the first few weeks. Many of our success stories report finding their life partner within 30-90 days.
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Band -->
    <section class="py-16 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-center">
        <div class="max-w-4xl mx-auto px-4">
             <h2 class="text-3xl font-bold mb-6">Your search for a perfect match starts here!</h2>
             <a href="{{ route('register') }}" class="inline-block bg-white text-brand-600 font-bold px-8 py-3.5 rounded-full shadow-lg hover:shadow-xl hover:bg-gray-50 transition transform hover:-translate-y-1">
                 Register Free
             </a>
        </div>
    </section>
@endsection
