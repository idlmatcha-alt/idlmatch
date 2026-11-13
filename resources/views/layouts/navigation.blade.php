<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm font-sans">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-1 group">
                        <span class="text-3xl font-bold text-brand-700 tracking-tight group-hover:text-brand-600 transition">IDEAL MARRIAGE CENTER</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-12 sm:flex">
                    <x-nav-link :href="route('profiles.index')" :active="request()->routeIs('profiles.*')"
                        class="text-[15px] font-medium text-gray-700 hover:text-brand-600 border-transparent hover:border-brand-500">
                        {{ __('Browse Profiles') }}
                    </x-nav-link>
                    <x-nav-link :href="route('success-stories.index')" :active="request()->routeIs('success-stories.*')"
                        class="text-[15px] font-medium text-gray-700 hover:text-brand-600 border-transparent hover:border-brand-500">
                        {{ __('Success Stories') }}
                    </x-nav-link>
                    <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')"
                        class="text-[15px] font-medium text-gray-700 hover:text-brand-600 border-transparent hover:border-brand-500">
                        {{ __('Membership Plans') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-3">
                @auth
                    <a href="{{ route('threads.index') }}"
                        class="relative p-2 text-gray-500 hover:text-brand-600 transition group" title="Messages">
                        <svg class="w-6 h-6 group-hover:scale-110 transition" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                            </path>
                        </svg>
                        @php $msgCount = Auth::user()->unreadMessagesCount(); @endphp
                        @if($msgCount > 0)
                            <span
                                class="absolute top-0 right-0 -mr-1 -mt-1 h-4 w-4 rounded-full bg-brand-600 text-[10px] font-bold text-white flex items-center justify-center ring-2 ring-white">
                                {{ $msgCount > 9 ? '9+' : $msgCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('dashboard') }}"
                        class="relative p-2 text-gray-500 hover:text-brand-600 transition group" title="Interests">
                        <svg class="w-6 h-6 group-hover:scale-110 transition" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                        @php $intCount = Auth::user()->unreadInterestsCount(); @endphp
                        @if($intCount > 0)
                            <span
                                class="absolute top-0 right-0 -mr-1 -mt-1 h-4 w-4 rounded-full bg-brand-600 text-[10px] font-bold text-white flex items-center justify-center ring-2 ring-white">
                                {{ $intCount > 9 ? '9+' : $intCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('favorites.index') }}"
                        class="relative p-2 text-gray-500 hover:text-brand-600 transition group" title="Shortlist">
                        <svg class="w-6 h-6 group-hover:scale-110 transition" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V5z M9 9h6 M9 13h6"></path>
                            </svg>
                    </a>

                    {{-- Subscription Plan Badge --}}
                    @php
                        $subscription = Auth::user()->getActiveSubscription();
                        $planName = $subscription ? $subscription->plan->name : 'Free';
                    @endphp
                    <a href="{{ route('packages.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-full border-2 
                            @if($planName === 'Free') border-gray-200 bg-gray-50 text-gray-600 hover:border-brand-300 hover:text-brand-600
                            @elseif($planName === 'Standard') border-brand-200 bg-brand-50 text-brand-700
                            @elseif($planName === 'Gold') border-amber-300 bg-amber-50 text-amber-700
                            @elseif($planName === 'Diamond') border-cyan-300 bg-cyan-50 text-cyan-700
                            @elseif($planName === 'Upper Class') border-fuchsia-300 bg-fuchsia-50 text-fuchsia-700
                            @endif
                            transition-all font-semibold text-sm">
                        @if($planName !== 'Free')
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                        <span>{{ $planName }}</span>
                    </a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center pl-3 pr-2 py-1.5 border border-gray-200 rounded-full text-sm font-medium text-gray-700 bg-white hover:text-brand-600 hover:border-brand-300 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-brand-500 transition ease-in-out duration-150 gap-2">
                                <div>{{ Auth::user()->name }}</div>
                                <div
                                    class="h-6 w-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('Dashboard') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-bold text-gray-600 hover:text-brand-600 transition px-3">Login</a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-6 py-2.5 bg-brand-600 border border-transparent rounded-full font-bold text-sm text-white hover:bg-brand-700 active:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-brand-500/30">
                        Free Registration
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link :href="route('profiles.index')" :active="request()->routeIs('profiles.*')">
                {{ __('Browse Profiles') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')">
                {{ __('Membership Plans') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('success-stories.index')"
                :active="request()->routeIs('success-stories.*')">
                {{ __('Success Stories') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-gray-100 bg-gray-50/50">
            @auth
                <div class="px-4 flex items-center gap-3">
                    <div
                        class="h-10 w-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-lg font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1 px-2">
                    <x-responsive-nav-link :href="route('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('threads.index')">
                        {{ __('Messages') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="flex flex-col gap-2 p-4">
                    <a href="{{ route('login') }}"
                        class="w-full text-center py-2 text-gray-600 font-semibold border border-gray-300 rounded-lg">Log
                        In</a>
                    <a href="{{ route('register') }}"
                        class="w-full text-center py-2 bg-brand-600 text-white font-semibold rounded-lg shadow-md">Free
                        Registration</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
