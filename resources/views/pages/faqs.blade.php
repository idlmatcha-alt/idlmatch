@extends('layouts.marketing')

@section('content')
    <section class="section max-w-4xl py-16">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-500">Help</p>
        <h1 class="mt-3 text-4xl font-semibold">Rishta &amp; Marriage FAQs</h1>
        <p class="mt-4 text-base leading-7 text-slate-600">
            Answers to the questions families ask most before registering on a matrimonial website: how to send an
            interest, how profiles are verified, when contact details are shared, what a rishta package covers and how
            to stay safe while searching for a match.
        </p>

        <div class="mt-10 space-y-6">
            @foreach ($faqs as $faq)
                <div class="card p-6">
                    <h2 class="text-lg font-semibold">{{ $faq['q'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $faq['a'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-12 card p-6 bg-slate-50">
            <h2 class="text-lg font-semibold">Ready to start your rishta search?</h2>
            <p class="mt-2 text-sm text-slate-600">
                Create a free biodata, browse verified profiles or compare matchmaking packages — and talk to a
                consultant at any point.
            </p>
            <div class="mt-4 flex flex-wrap gap-3 text-sm font-semibold">
                <a href="{{ route('register') }}" class="btn-primary">Create Free Profile</a>
                <a href="{{ route('profiles.index') }}" class="btn-secondary">Browse Brides &amp; Grooms</a>
                <a href="{{ route('packages.index') }}" class="btn-secondary">See Packages</a>
                <a href="{{ route('contact') }}" class="btn-secondary">Contact Us</a>
            </div>
        </div>
    </section>
@endsection
