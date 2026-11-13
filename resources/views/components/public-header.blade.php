<header x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 shadow-none">

    <!-- Top Header Row: White Background with Logo and Auth Buttons -->
    <div class="bg-white/70 backdrop-blur-md border-b border-white/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between py-3">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <picture>
                    <source srcset="{{ asset('images/logo.webp') }}" type="image/webp">
                    <img src="{{ asset('images/logo-240.png') }}" alt="Ideal Marriage Center logo" class="h-12 w-auto rounded-lg shadow-sm"
                        width="48" height="48" decoding="async">
                </picture>
                <span class="text-2xl font-bold tracking-tight font-sans" style="color: #881337;">IDEAL MARRIAGE CENTER</span>
            </a>

            <!-- Auth Buttons (Desktop) -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded-lg text-sm font-bold transition shadow-sm"
                            style="background: linear-gradient(135deg, #E91E63, #C2185B); color: white;">Dashboard</a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold border-2 transition hover:bg-pink-50"
                                style="border-color: #E91E63; color: #E91E63;">
                                Log Out
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('register') }}"
                        class="px-5 py-2 rounded-lg text-sm font-bold border-2 transition hover:bg-pink-50"
                        style="border-color: #E91E63; color: #E91E63;">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Register
                    </a>
                    <a href="{{ route('login') }}"
                        class="px-5 py-2 rounded-lg text-sm font-bold border-2 transition hover:bg-pink-50"
                        style="border-color: #E91E63; color: #E91E63;">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <button @click="open = !open" class="md:hidden p-2 rounded-lg transition" style="color: #E91E63;">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Bottom Header Row: Maroon Background with Navigation -->
    <div class="hidden md:block" style="background: linear-gradient(135deg, #A1234D, #8B1E3F);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-center gap-8 py-4">
                <a href="{{ route('home') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">HOME</a>

                <!-- About Us Dropdown -->
                <div x-data="{ aboutOpen: false }" @mouseenter="aboutOpen = true" @mouseleave="aboutOpen = false"
                    class="relative">
                    <button @click="aboutOpen = !aboutOpen"
                        class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide flex items-center gap-1">
                        ABOUT US
                        <svg class="w-3 h-3 transition-transform duration-200" :class="aboutOpen ? 'rotate-180' : ''"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="aboutOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute left-0 pt-3 z-50">
                        <div class="about-menu-panel overflow-hidden rounded-2xl bg-white p-3 shadow-2xl">
                            <div class="grid grid-cols-2 gap-1">
                                @php
                                    $aboutMenu = [
                                        ['route' => 'about', 'label' => 'About Us', 'text' => 'Our story and values', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        ['route' => 'about.hire-consultant', 'label' => 'Hire Consultant', 'text' => 'One-to-one matchmaking', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                        ['route' => 'about.our-services', 'label' => 'Our Services', 'text' => 'Verified rishta services', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                        ['route' => 'about.legal-advisor', 'label' => 'Legal Advisor', 'text' => 'Nikah and paperwork', 'icon' => 'M3 6l9-3 9 3M4 21h16M6 21V9m4 12V9m4 12V9m4 12V9'],
                                        ['route' => 'about.leadership', 'label' => 'Leadership', 'text' => 'The people behind us', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                                        ['route' => 'about.grooming-consultant', 'label' => 'Grooming Consultant', 'text' => 'Look and feel your best', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                                        ['route' => 'about.marriage-counseling', 'label' => 'Marriage Counseling', 'text' => 'Confidential guidance', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                        ['route' => 'safety', 'label' => 'Safety & Security', 'text' => 'How we protect you', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                                        ['route' => 'about.affiliate', 'label' => 'Affiliate', 'text' => 'Earn by referring', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        ['route' => 'blog.index', 'label' => 'Blog', 'text' => 'Advice and stories', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                                    ];
                                @endphp
                                @foreach ($aboutMenu as $item)
                                    <a href="{{ route($item['route']) }}"
                                        class="about-menu-item group flex items-start gap-3 rounded-xl px-3 py-2.5 transition hover:bg-pink-50">
                                        <span class="about-icon-sm">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $item['icon'] }}" />
                                            </svg>
                                        </span>
                                        <span class="min-w-0">
                                            <span
                                                class="block text-sm font-bold text-slate-800 transition group-hover:text-brand-700">{{ $item['label'] }}</span>
                                            <span class="block text-xs text-slate-500">{{ $item['text'] }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                            <div class="mt-2 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                <span class="text-xs text-slate-500">Not sure where to start?</span>
                                <a href="{{ route('contact') }}"
                                    class="text-xs font-bold text-brand-600 hover:text-brand-700">Talk to a consultant
                                    &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profiles.index') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">SEARCH</a>
                <a href="{{ route('profiles.index', ['sort' => 'newest']) }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">BEST
                    PROPOSALS</a>
                <a href="{{ route('abroad.proposals') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">ABROAD
                    PROPOSAL</a>
                <a href="{{ route('elite.matrimonial') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">ELITE
                    MATRIMONIAL</a>
                <a href="{{ route('packages.index') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">PACKAGES</a>
                <a href="{{ route('career') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">CAREER</a>
                <a href="{{ route('blog.index') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">BLOG</a>
                <a href="{{ route('contact') }}"
                    class="text-white/90 hover:text-white text-xs font-bold transition uppercase tracking-wide">CONTACT
                    US</a>
            </nav>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1" class="md:hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('home') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Home</a>
            <div x-data="{ aboutOpen: false }">
                <button @click="aboutOpen = !aboutOpen"
                    class="flex w-full items-center justify-between py-2 text-sm font-medium text-slate-700 transition hover:text-pink-600">
                    About Us
                    <svg class="h-4 w-4 transition-transform duration-200" :class="aboutOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="aboutOpen" x-cloak class="about-subnav mb-1 ml-3 space-y-1 pl-3">
                    @foreach ($aboutMenu as $item)
                        <a href="{{ route($item['route']) }}"
                            class="block py-1.5 text-sm text-slate-600 transition hover:text-pink-600">{{ $item['label'] }}</a>
                    @endforeach
                    <a href="{{ route('faqs') }}"
                        class="block py-1.5 text-sm text-slate-600 transition hover:text-pink-600">FAQs</a>
                </div>
            </div>
            <a href="{{ route('profiles.index') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Search</a>
            <a href="{{ route('profiles.index', ['sort' => 'newest']) }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Best
                Proposals</a>
            <a href="{{ route('abroad.proposals') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Abroad
                Proposal</a>
            <a href="{{ route('elite.matrimonial') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Elite
                Matrimonial</a>
            <a href="{{ route('packages.index') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Packages</a>
            <a href="{{ route('career') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Career</a>
            <a href="{{ route('blog.index') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Blog</a>
            <a href="{{ route('contact') }}"
                class="block text-slate-700 hover:text-pink-600 text-sm font-medium py-2 transition">Contact</a>

            @auth
                <a href="{{ route('dashboard') }}"
                    class="block w-full text-center py-2 px-4 rounded-lg text-sm font-bold text-white transition-colors mt-4"
                    style="background: linear-gradient(135deg, #E91E63, #C2185B);">Dashboard</a>
                
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="block w-full text-center py-2 px-4 rounded-lg text-sm font-bold border-2 transition"
                        style="border-color: #E91E63; color: #E91E63;">
                        Log Out
                    </button>
                </form>
            @else
                <a href="{{ route('register') }}"
                    class="block w-full text-center py-2 px-4 rounded-lg text-sm font-bold border-2 transition mt-4"
                    style="border-color: #E91E63; color: #E91E63;">Register</a>
                <a href="{{ route('login') }}"
                    class="block w-full text-center py-2 px-4 rounded-lg text-sm font-bold border-2 transition"
                    style="border-color: #E91E63; color: #E91E63;">Sign In</a>
            @endauth
        </div>
    </div>
</header>
