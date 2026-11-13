@extends('layouts.marketing')


@section('content')
    <section class="section max-w-4xl py-16">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-500">Support</p>
        <h1 class="mt-3 text-4xl font-semibold">Contact Us</h1>
        <p class="mt-4 text-slate-600">Have questions or need help? Send us a note and our team will respond within 24
            hours.</p>

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

            <div class="contact-couple-marquee mt-8 rounded-2xl"
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

        <div class="mt-10 card p-6">
            <form method="POST" action="{{ route('contact.submit') }}" class="grid gap-4">
                @csrf
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                <div>
                    <label class="text-sm font-medium text-slate-600">Your Name</label>
                    <input type="text" name="name" class="mt-2 w-full field" placeholder="Full name">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Email</label>
                    <input type="email" name="email" class="mt-2 w-full field" placeholder="you@email.com">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Message</label>
                    <textarea name="message" rows="5" class="mt-2 w-full field"
                        placeholder="Tell us how we can help"></textarea>
                </div>
                <button type="submit" class="btn-primary w-fit">Send Message</button>
            </form>
        </div>

        <div class="mt-6 card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Subscribe for matchmaking tips</h2>
            <p class="mt-2 text-sm text-slate-600">Get marriage guidance, success stories and member offers in your
                inbox. No spam.</p>

            @if (session('subscribe_status'))
                <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                    {{ session('subscribe_status') }}
                </div>
            @endif

            @error('subscriber_email')
                <div class="mt-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('contact.subscribe') }}" class="mt-4 grid gap-3">
                @csrf
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                <label for="subscriber_email" class="text-sm font-medium text-slate-600">Email address</label>
                <input id="subscriber_email" type="email" name="subscriber_email" value="{{ old('subscriber_email') }}"
                    required class="w-full field" placeholder="you@email.com">
                <button type="submit" class="btn-primary w-fit">Subscribe</button>
                <p class="text-xs text-slate-500">By subscribing you agree to receive occasional emails from Ideal
                    Marriage Center.</p>
            </form>
        </div>
    </section>
@endsection