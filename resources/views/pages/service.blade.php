@extends('layouts.marketing')


@section('content')
    <section class="about-hero-bg relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <nav class="mb-8 text-xs font-bold uppercase tracking-widest text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
                <span class="mx-2">/</span>
                <span class="text-brand-600">{{ $page['eyebrow'] }}</span>
            </nav>

            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                <div>
                    <span class="about-eyebrow">{{ $page['eyebrow'] }}</span>
                    <h1 class="about-h1 mt-5 font-display font-bold text-slate-900">
                        {{ $page['title'] }}</h1>
                    <p class="mt-5 text-lg leading-relaxed text-slate-600">{{ $page['lead'] }}</p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ $page['cta_url'] ?? route($page['cta_route']) }}" class="btn-primary">
                            {{ $page['cta_label'] }}
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a href="{{ route('contact') }}" class="btn-secondary">Talk to us</a>
                    </div>
                </div>

                <div class="relative">
                    <div class="about-halo"></div>
                    <img src="{{ asset($page['image']) }}" alt="{{ $page['title'] }}" class="about-hero-img"
                        loading="eager" width="1200" height="800">
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-500">{{ $page['highlights_eyebrow'] ?? 'What you get' }}</p>
                <h2 class="mt-3 text-3xl font-bold text-slate-900 font-display">{{ $page['highlights_title'] ?? 'Built around your family' }}</h2>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2">
                @foreach ($page['highlights'] as $highlight)
                    <div class="about-highlight-card p-7">
                        <div class="about-icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">{{ $highlight['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $highlight['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-500">How it works</p>
                <h2 class="mt-3 text-3xl font-bold text-slate-900 font-display">
                    {{ $page['steps_title'] ?? 'Four simple steps' }}</h2>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($page['steps'] as $index => $step)
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                        <span
                            class="text-4xl font-bold text-brand-100 font-display">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-2 text-base font-bold text-slate-900">{{ $step }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="about-cta-band overflow-hidden rounded-3xl px-8 py-12 text-center md:px-16">
                <h2 class="text-3xl font-bold text-white font-display md:text-4xl">{{ $page['cta_title'] }}</h2>
                <p class="mx-auto mt-4 max-w-2xl text-white/80">{{ $page['cta_text'] }}</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ $page['cta_url'] ?? route($page['cta_route']) }}" class="about-cta-white">
                        {{ $page['cta_label'] }}
                    </a>
                    <a href="{{ route('packages.index') }}" class="about-cta-outline">
                        View packages
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
