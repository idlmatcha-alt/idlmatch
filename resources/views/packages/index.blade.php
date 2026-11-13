@extends('layouts.marketing')


@section('content')
<section class="relative min-h-screen bg-slate-50 overflow-hidden py-24">
    <!-- Modern Abstract Background (Light) -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-brand-500/5 rounded-full blur-3xl mix-blend-multiply animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-purple-600/5 rounded-full blur-3xl mix-blend-multiply animate-pulse"></div>
        <div class="absolute inset-0 opacity-[0.4] brightness-100 contrast-150 mix-blend-overlay"
            style="background-image:url('{{ asset('images/noise.svg') }}')"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-brand-600 font-bold tracking-widest uppercase text-xs mb-3 font-display">Premium Memberships</h2>
            <h1 class="text-5xl md:text-7xl font-display font-bold text-slate-900 mb-8 tracking-tight">
                Invest in your <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-purple-600 to-brand-600 animate-text-shimmer bg-[length:200%_auto]">Future Together</span>
            </h1>
            <p class="text-xl text-slate-600 leading-relaxed max-w-2xl mx-auto">
                Unlock exclusive features, verified status, and direct contact access to find your perfect match faster.
            </p>
        </div>

        @if(isset($pendingSubscription) && $pendingSubscription)
            <div class="max-w-3xl mx-auto mb-12 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            You have a pending subscription verification for the <strong>{{ $pendingSubscription->plan->name }}</strong> plan. 
                            You cannot select a new plan until this is processed.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 items-start">
            @forelse($plans as $plan)
                <div class="relative group h-full">
                    <!-- Card Border/Glow Container -->
                    <div class="absolute -inset-[1px] bg-gradient-to-b from-slate-200 to-white rounded-[2rem] opacity-100 group-hover:opacity-100 transition duration-500"></div>
                    @if($plan->name == 'Upper Class')
                        <div class="absolute -inset-[1px] bg-gradient-to-br from-brand-400 via-purple-500 to-pink-400 rounded-[2rem] opacity-100 blur-sm group-hover:blur-md transition duration-500"></div>
                    @endif
                    
                    <!-- Main Card Content -->
                    <div class="relative flex flex-col h-full bg-white rounded-[1.9rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/50 transition-transform duration-300 group-hover:-translate-y-1">
                        @if(isset($currentPlanId) && $currentPlanId == $plan->id)
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black tracking-wide text-white bg-green-500 shadow-lg shadow-green-500/30 uppercase">
                                    Current Plan
                                </span>
                            </div>
                        @elseif($plan->name == 'Upper Class')
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black tracking-wide text-white bg-gradient-to-r from-brand-600 to-amber-500 shadow-lg shadow-brand-500/30 uppercase">
                                    Most Popular
                                </span>
                            </div>
                        @endif

                        <div class="mb-6 mt-2">
                            <h3 class="text-xl font-bold text-slate-900">{{ $plan->name }}</h3>
                            <div class="flex items-baseline gap-1 mt-4">
                                @if($plan->price == 0)
                                    <span class="text-4xl font-black text-slate-900 tracking-tight">Free</span>
                                @else
                                    <span class="text-2xl font-bold text-slate-500">PKR</span>
                                    <span class="text-4xl font-black text-slate-900 tracking-tight">{{ number_format($plan->price) }}</span>
                                @endif
                            </div>
                            <p class="text-sm font-medium text-slate-500 mt-2 bg-slate-50 inline-block px-3 py-1 rounded-lg">
                                {{ $plan->duration_days }} Days Access
                            </p>
                        </div>

                        <hr class="border-slate-100 mb-6">

                        <ul class="space-y-4 mb-8 flex-1">
                            <!-- Feature Item -->
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-brand-50 flex items-center justify-center mt-0.5">
                                    <svg class="h-3.5 w-3.5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <span class="text-slate-600 text-sm font-medium leading-relaxed">
                                    View <strong class="text-slate-900">{{ $plan->limits_json['profile_views'] }}</strong> Profiles
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full {{ $plan->limits_json['contact_access'] ? 'bg-brand-50' : 'bg-slate-50' }} flex items-center justify-center mt-0.5">
                                    <svg class="h-3.5 w-3.5 {{ $plan->limits_json['contact_access'] ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span class="text-sm {{ $plan->limits_json['contact_access'] ? 'text-slate-700' : 'text-slate-400 line-through' }} font-medium leading-relaxed">Direct Contact Access</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full {{ $plan->limits_json['verified_badge'] ? 'bg-brand-50' : 'bg-slate-50' }} flex items-center justify-center mt-0.5">
                                    <svg class="h-3.5 w-3.5 {{ $plan->limits_json['verified_badge'] ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm {{ $plan->limits_json['verified_badge'] ? 'text-slate-700' : 'text-slate-400 line-through' }} font-medium leading-relaxed">Verified Badge</span>
                            </li>
                             <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-brand-50 flex items-center justify-center mt-0.5">
                                    <svg class="h-3.5 w-3.5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </div>
                                <span class="text-slate-600 text-sm font-medium leading-relaxed">Priority Support</span>
                            </li>
                        </ul>

                        @if(isset($currentPlanId) && $currentPlanId == $plan->id)
                            <button disabled class="block w-full py-4 px-6 rounded-xl text-center font-bold text-sm tracking-wide bg-green-500 text-white cursor-default shadow-lg shadow-green-500/20">
                                Current Plan
                            </button>
                        @elseif(isset($pendingSubscription) && $pendingSubscription)
                            <button disabled class="block w-full py-4 px-6 rounded-xl text-center font-bold text-sm tracking-wide bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">
                                Verification Pending
                            </button>
                        @else
                            <a href="{{ route('packages.select', $plan) }}" 
                               class="block w-full py-4 px-6 rounded-xl text-center font-bold text-sm tracking-wide transition-all transform hover:-translate-y-0.5 active:scale-[0.98] text-white {{ $plan->name == 'Upper Class' ? 'bg-gradient-to-r from-brand-500 to-brand-600 hover:to-brand-700' : 'bg-brand-800 hover:bg-brand-900' }}">
                                Select Plan
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="lg:col-span-4 rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">No packages available right now</h3>
                    <p class="mt-2 text-sm text-slate-600">Please check back shortly or contact support.</p>
                </div>
            @endforelse
        </div>

        <!-- Trust footer -->
        <div class="mt-24 text-center">
            <p class="text-slate-400 text-sm font-medium mb-6 uppercase tracking-widest">Secure Payments Supported</p>
            <div class="flex flex-wrap justify-center gap-4 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="px-6 py-3 bg-white rounded-lg border border-slate-200 text-slate-500 font-bold text-sm shadow-sm">BANK TRANSFER</div>
                <div class="px-6 py-3 bg-white rounded-lg border border-slate-200 text-slate-500 font-bold text-sm shadow-sm">JAZZCASH</div>
                <div class="px-6 py-3 bg-white rounded-lg border border-slate-200 text-slate-500 font-bold text-sm shadow-sm">EASYPAISA</div>
            </div>
            <p class="mt-8 text-slate-500 text-sm max-w-2xl mx-auto">
                Payments are processed securely. Your membership will be activated immediately upon verification of payment.
            </p>
        </div>
    </div>

</section>

@endsection
