@php
    $supportPhone = '923122294445';
    $supportText = 'Assalam o Alaikum. I am contacting you from this page: ' . request()->fullUrl();
    $supportWhatsappUrl = 'https://api.whatsapp.com/send/?phone=' . $supportPhone . '&text=' . urlencode($supportText) . '&type=phone_number&app_absent=0';
    $footerBlogPosts = \App\Models\BlogPost::query()
        ->select(['id', 'title', 'slug', 'status', 'published_at', 'created_at'])
        ->where('status', 'published')
        ->where(function ($query) {
            $query->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })
        ->inRandomOrder()
        ->take(30)
        ->get()
        ->unique(fn (\App\Models\BlogPost $post) => \Illuminate\Support\Str::lower(trim((string) $post->title)))
        ->take(4)
        ->values();
@endphp

<!-- Footer (Shaadi Style: Darker/Textured) -->
<footer id="site-footer" class="bg-gray-100 border-t border-gray-200 mt-auto">
    <!-- AdSense Ad - Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <!-- footer-ads -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-7126798303930967"
             data-ad-slot="9308259437"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- SEO Description Block -->
        <div class="mb-8 pb-8 border-b border-gray-200">
            <p class="text-xs text-gray-600 leading-relaxed">
                Ideal Marriage Center is Pakistan's leading matchmaking service with millions of success stories. Founded
                with a mission to help people find their perfect life partner, we combine advanced AI technology
                with traditional matchmaking values. Our verified profiles, secure platform, and dedicated
                customer support make us the trusted choice for matrimonial services. With features like
                AI-powered matching, verified badges, and personalized recommendations, we've helped thousands
                of couples find lasting love. Join millions who trust Ideal Marriage Center for safe, authentic, and
                successful matchmaking.
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-7 gap-8">
            <div class="col-span-2 lg:col-span-1">
                <div class="text-xl font-bold font-sans text-gray-800">IDEAL MARRIAGE CENTER</div>
                <p class="mt-4 text-xs text-gray-500 leading-relaxed">The World's No.1 Matchmaking Service.
                    Trusted by millions of people worldwide.</p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Company</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('about') }}"
                            class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">About Us</a>
                    </li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Awards</a>
                    </li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Careers</a>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Privacy & You
                </h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('privacy') }}"
                            class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Privacy
                            Policy</a></li>
                    <li><a href="{{ route('terms') }}"
                            class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Terms of
                            Use</a>
                    </li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Be
                            Safe
                            Online</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Need Help?</h3>
                <ul class="space-y-2">
                    @guest
                        <li><a href="{{ route('login') }}"
                                class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Member
                                Login</a>
                        </li>
                        <li><a href="{{ route('register') }}"
                                class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Sign Up</a>
                        </li>
                    @else
                        <li><a href="{{ route('dashboard') }}"
                                class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Dashboard</a>
                        </li>
                    @endguest
                    <li><a href="{{ route('contact') }}"
                            class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline">Customer
                            Support</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Blog Resources</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('blog.index') }}"
                            class="text-xs font-semibold text-gray-700 hover:text-[#E91E63] hover:underline">
                            Blog Home
                        </a>
                    </li>
                    @foreach($footerBlogPosts as $blogLinkPost)
                        <li>
                            <a href="{{ route('blog.show', $blogLinkPost) }}"
                                class="text-xs text-gray-600 hover:text-[#E91E63] hover:underline line-clamp-1">
                                {{ $blogLinkPost->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-span-2 lg:col-span-2">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Follow Us</h3>
                <div class="flex space-x-3 text-gray-400 mb-6">
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/idlmatch" target="_blank" rel="noopener noreferrer"
                        class="hover:text-[#1877F2] transition"><span class="sr-only">Facebook</span><svg
                            class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                clip-rule="evenodd"></path>
                        </svg></a>
                    <!-- X -->
                    <a href="https://x.com/Idlmatch" target="_blank" rel="noopener noreferrer"
                        class="hover:text-[#000000] transition"><span class="sr-only">X</span><svg class="h-5 w-5"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84">
                            </path>
                        </svg></a>
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/idlmatch" target="_blank" rel="noopener noreferrer"
                        class="hover:text-[#E4405F] transition"><span class="sr-only">Instagram</span><svg
                            class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" />
                        </svg></a>
                    <!-- YouTube -->
                    <a href="https://www.youtube.com/@Idlmatch" target="_blank" rel="noopener noreferrer"
                        class="hover:text-[#FF0000] transition"><span class="sr-only">YouTube</span><svg
                            class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg></a>
                    <!-- WhatsApp -->
                    <a href="{{ $supportWhatsappUrl }}" target="_blank" rel="noopener noreferrer"
                        class="hover:text-[#25D366] transition"><span class="sr-only">WhatsApp</span><svg
                            class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-4.924 1.253l-.354.214-3.675-.964.984 3.602-.235.374a9.847 9.847 0 001.515 5.963 9.93 9.93 0 005.11 3.238l.46.053a9.88 9.88 0 005.01-1.868l.358-.213 3.686.964-.988-3.603.236-.374a9.879 9.879 0 00-1.516-5.962A9.928 9.928 0 0012 5.978a9.86 9.86 0 00-.051 0z" />
                        </svg></a>
                </div>
                <!-- App Download Badges -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Get the App
                    </h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('coming-soon') }}" aria-label="Download on the App Store"
                            class="flex min-w-0 flex-1 items-center justify-center gap-1 bg-black text-white px-2 py-1.5 rounded-lg hover:bg-gray-800 transition">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z" />
                            </svg>
                            <span class="min-w-0 text-left leading-none">
                                <span class="block truncate text-[10px]">Download on the</span>
                                <span class="block truncate text-[11px] font-semibold">App Store</span>
                            </span>
                        </a>
                        <a href="{{ route('coming-soon') }}" aria-label="Get it on Google Play"
                            class="flex min-w-0 flex-1 items-center justify-center gap-1 bg-black text-white px-2 py-1.5 rounded-lg hover:bg-gray-800 transition">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M3.609 1.814L13.792 12 3.61 22.186a.996.996 0 0 1-.61-.92V2.734a1 1 0 0 1 .609-.92zm10.89 10.893l2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.198l2.807 1.626a1 1 0 0 1 0 1.73l-2.808 1.626L15.206 12l2.492-2.491zM5.864 2.658L16.802 8.99l-2.303 2.303-8.635-8.635z" />
                            </svg>
                            <span class="min-w-0 text-left leading-none">
                                <span class="block truncate text-[10px]">GET IT ON</span>
                                <span class="block truncate text-[11px] font-semibold">Google Play</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 border-t border-gray-200 pt-8">
            <p class="text-xs text-gray-500">

 &copy; {{ date('Y') }} Ideal Marriage Center. All rights reserved. <x-credit />
            </p>
        </div>
    </div>
</footer>
