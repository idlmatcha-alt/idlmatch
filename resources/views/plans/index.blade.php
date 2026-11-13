@extends('layouts.marketing')


@section('content')
    <section class="section py-16">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-500">Membership</p>
        <h1 class="mt-3 text-4xl font-semibold">Membership Plans</h1>
        <p class="mt-4 text-slate-600">Choose the plan that fits your journey.</p>

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($plans as $plan)
                <div class="card p-6">
                    <h3 class="font-display text-2xl">{{ $plan->name }}</h3>
                    <p class="mt-3 text-3xl font-semibold">{{ $plan->currency }} {{ number_format($plan->price) }}</p>
                    <ul class="mt-4 space-y-2 text-sm text-slate-600">
                        <li>Daily interests: {{ $plan->limits_json['daily_interests'] ?? '-' }}</li>
                        <li>Daily messages: {{ $plan->limits_json['daily_messages'] ?? '-' }}</li>
                        <li>Contact details: {{ $plan->limits_json['contact_access'] ?? 'Yes' }}</li>
                    </ul>
                    @auth
                        <a href="{{ route('plans.subscribe', $plan) }}" class="btn-primary mt-6 w-full">Subscribe</a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary mt-6 w-full">Get started</a>
                    @endauth
                </div>
            @endforeach
        </div>
    </section>
@endsection