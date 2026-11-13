@extends('layouts.marketing')


@section('content')
    <section class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-20">
        <div class="max-w-3xl w-full px-4 text-center">
            <div class="mb-8">
                <span class="inline-block p-4 rounded-full bg-pink-100 text-pink-600 mb-4 animate-bounce">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </span>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-gray-900 mb-4">Mobile App Coming Soon!</h1>
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    We're hard at work building the best matchmaking experience for your mobile device.
                    The Ideal Marriage Center app will be available on the App Store and Google Play Store very shortly.
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Be the first to know when we launch</h3>
                <form action="{{ route('contact') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="email" placeholder="Enter your email" class="flex-1 field rounded-full" required>
                    <button type="submit"
                        class="btn-primary rounded-full px-8 py-3 font-bold shadow-lg hover:shadow-pink-200 transition-all">
                        Notify Me
                    </button>
                </form>
                <p class="mt-4 text-sm text-gray-500">For now, enjoy our fully responsive website on your mobile browser!
                </p>
            </div>

            <div class="mt-12 flex items-center justify-center gap-3 opacity-30">
                <span class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl">
                    <svg class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z" />
                    </svg>
                    <span class="text-left leading-none">
                        <span class="block text-[10px]">Coming soon on the</span>
                        <span class="block text-[11px] font-semibold">App Store</span>
                    </span>
                </span>
                <span class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl">
                    <svg class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M3.609 1.814L13.792 12 3.61 22.186a.996.996 0 0 1-.61-.92V2.734a1 1 0 0 1 .609-.92zm10.89 10.893l2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.198l2.807 1.626a1 1 0 0 1 0 1.73l-2.808 1.626L15.206 12l2.492-2.491zM5.864 2.658L16.802 8.99l-2.303 2.303-8.635-8.635z" />
                    </svg>
                    <span class="text-left leading-none">
                        <span class="block text-[10px]">Coming soon on</span>
                        <span class="block text-[11px] font-semibold">Google Play</span>
                    </span>
                </span>
            </div>

            <div class="mt-12">
                <a href="{{ route('home') }}" class="text-pink-600 font-bold hover:underline">← Back to Home</a>
            </div>
        </div>
    </section>
@endsection