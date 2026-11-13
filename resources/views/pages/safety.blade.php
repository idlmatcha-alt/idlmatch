@extends('layouts.marketing')


@section('content')
    <section class="about-hero-bg relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <nav class="mb-8 text-xs font-bold uppercase tracking-widest text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
                <span class="mx-2">/</span>
                <span class="text-brand-600">Safety &amp; Security</span>
            </nav>

            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                <div>
                    <span class="about-eyebrow">Safety &amp; Security</span>
                    <h1 class="about-h1 mt-5 font-bold text-slate-900 font-display">Your safety comes first</h1>
                    <p class="mt-5 text-lg leading-relaxed text-slate-600">
                        Every profile is reviewed, every report is investigated and every contact detail stays private until
                        you decide otherwise. Here is how we protect your search — and how you can protect yourself.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}" class="btn-primary">Report a concern</a>
                        <a href="{{ route('faqs') }}" class="btn-secondary">Read the FAQs</a>
                    </div>
                </div>

                <div class="relative">
                    <div class="about-halo"></div>
                    <img src="{{ asset('images/about/safety.jpg') }}" alt="Verification team at Ideal Marriage Center"
                        class="about-hero-img" loading="eager" width="1200" height="800">
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-500">On our side</p>
                <h2 class="mt-3 text-3xl font-bold text-slate-900 font-display">How we keep the platform safe</h2>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
        ['Profile verification', 'CNIC and photo checks plus manual review before a profile goes live.'],
        ['Privacy by default', 'Contact details and photos are hidden until mutual interest is accepted.'],
        ['Report & block tools', 'One tap to report a profile or block contact — reviewed by our team quickly.'],
        ['Secure messaging', 'Talk on the platform first; we never ask members to share OTPs or bank details.'],
    ] as $item)
                    <div class="card p-6">
                        <div class="about-icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-slate-900">{{ $item[0] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $item[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-500">On your side</p>
                <h2 class="mt-3 text-3xl font-bold text-slate-900 font-display">Safety tips for members</h2>
            </div>

            <div class="mt-10 space-y-4">
                @foreach ([
        ['Keep conversations on the platform', 'Avoid moving to WhatsApp or personal email too early. Use our secure messaging to get to know the person first.'],
        ['Never send money', 'Be aware of scammers who ask for money for a visa, travel or a medical emergency. Ideal Marriage Center will never ask you to transfer money to another user.'],
        ['Video call before meeting', 'Arrange a video call to confirm the person matches their photos and to feel comfortable before meeting in person.'],
        ['Meet in public places', 'For the first meeting choose a public place, and tell a family member or friend where you are going.'],
        ['Involve your family early', 'Rishtas work best when families are part of the conversation from the beginning — it is also the strongest protection against fraud.'],
        ['Trust your instincts', 'If something feels wrong, stop, report the profile and contact our team. There is no rush.'],
    ] as $index => $tip)
                    <div class="flex gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                        <span class="text-2xl font-bold text-brand-100 font-display">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">{{ $tip[0] }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $tip[1] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="about-cta-band overflow-hidden rounded-3xl px-8 py-12 text-center md:px-16">
                <h2 class="text-3xl font-bold text-white font-display md:text-4xl">Something does not feel right?</h2>
                <p class="mx-auto mt-4 max-w-2xl text-white/80">Report the profile or message our team directly. We review
                    every report and act quickly.</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('contact') }}" class="about-cta-white">Contact our safety team</a>
                    <a href="{{ route('about.our-services') }}" class="about-cta-outline">See our services</a>
                </div>
            </div>
        </div>
    </section>
@endsection
