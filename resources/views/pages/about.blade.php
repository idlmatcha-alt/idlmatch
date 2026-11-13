@extends('layouts.marketing')


@section('content')
    <section class="about-hero-bg relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                <div>
                    <span class="about-eyebrow">About Us</span>
                    <h1 class="about-h1 mt-5 font-bold text-slate-900 font-display">
                        About Ideal Marriage Center</h1>
                    <p class="mt-5 text-lg leading-relaxed text-slate-600">
                        Ideal Marriage Center is a premium matrimonial platform focused on Pakistan and the global
                        Pakistani community. We pair modern matchmaking tools with family-first values to help you find
                        the right partner with confidence.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn-primary">Create free profile</a>
                        <a href="{{ route('contact') }}" class="btn-secondary">Talk to us</a>
                    </div>
                </div>

                <div class="relative">
                    <div class="about-halo"></div>
                    <img src="{{ asset('images/about/about-hero.jpg') }}"
                        alt="Family meeting a couple at Ideal Marriage Center" class="about-hero-img" loading="eager"
                        width="1200" height="800">
                </div>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm">
                    <p class="text-4xl font-bold text-brand-600 font-display">15,000+</p>
                    <p class="mt-2 text-sm font-semibold text-slate-600">Verified profiles</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm">
                    <p class="text-4xl font-bold text-brand-600 font-display">2,500+</p>
                    <p class="mt-2 text-sm font-semibold text-slate-600">Successful rishtas</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm">
                    <p class="text-4xl font-bold text-brand-600 font-display">40+</p>
                    <p class="mt-2 text-sm font-semibold text-slate-600">Countries served</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-500">Why families choose us</p>
                <h2 class="mt-3 text-3xl font-bold text-slate-900 font-display">Serious matchmaking, not swiping</h2>
                <p class="mt-4 text-slate-600">Every profile is reviewed by people, every introduction is agreed by both
                    families, and every step is handled with the privacy a rishta deserves.</p>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2">
                <div class="card p-7">
                    <h3 class="text-lg font-bold text-slate-900">Trusted profiles</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Email verification, CNIC checks, moderation and
                        activity checks keep the community genuine.</p>
                </div>
                <div class="card p-7">
                    <h3 class="text-lg font-bold text-slate-900">Privacy controls</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Control visibility of contact details and photos
                        until mutual interest is accepted.</p>
                </div>
                <div class="card p-7">
                    <h3 class="text-lg font-bold text-slate-900">Human matchmakers</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Our consultants understand family values,
                        culture and expectations — and suggest rishtas that fit them.</p>
                </div>
                <div class="card p-7">
                    <h3 class="text-lg font-bold text-slate-900">Support beyond the match</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Legal guidance, counseling and wedding support are
                        available to every member who needs them.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-500">Explore</p>
                <h2 class="mt-3 text-3xl font-bold text-slate-900 font-display">More about Ideal Marriage Center</h2>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('about.hire-consultant') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Hire a Consultant</span>
                    <p class="mt-1 text-xs text-slate-500">One-to-one help from a senior matchmaker</p>
                </a>
                <a href="{{ route('about.our-services') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Our Services</span>
                    <p class="mt-1 text-xs text-slate-500">Verified profiles and safe introductions</p>
                </a>
                <a href="{{ route('about.legal-advisor') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Legal Advisor</span>
                    <p class="mt-1 text-xs text-slate-500">Nikah, contracts and immigration paperwork</p>
                </a>
                <a href="{{ route('about.leadership') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Leadership</span>
                    <p class="mt-1 text-xs text-slate-500">The people behind the centre</p>
                </a>
                <a href="{{ route('about.grooming-consultant') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Grooming Consultant</span>
                    <p class="mt-1 text-xs text-slate-500">Present yourself with confidence</p>
                </a>
                <a href="{{ route('about.marriage-counseling') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Marriage Counseling</span>
                    <p class="mt-1 text-xs text-slate-500">Confidential pre and post-marriage sessions</p>
                </a>
                <a href="{{ route('safety') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Safety & Security</span>
                    <p class="mt-1 text-xs text-slate-500">How we keep your search safe</p>
                </a>
                <a href="{{ route('about.affiliate') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Affiliate Program</span>
                    <p class="mt-1 text-xs text-slate-500">Earn by referring families</p>
                </a>
                <a href="{{ route('blog.index') }}"
                    class="group rounded-xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-brand-600">Blog</span>
                    <p class="mt-1 text-xs text-slate-500">Advice for the rishta journey</p>
                </a>
            </div>
        </div>
    </section>
@endsection
