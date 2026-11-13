<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Code from Magzel.com | For support contact hellomagzel@gmail.com -->

    <x-seo-meta />

    <!-- Impact is a system font, no external loading needed -->

    <x-frontend-assets />

    <x-meta-pixel />

    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7126798303930967"
         crossorigin="anonymous"></script>

    @stack('styles')
    @stack('meta')
</head>

<body class="antialiased font-sans">


    <div class="relative min-h-screen bg-white">
        <!-- Two-Tier Header -->
        <x-public-header />

        <main style="padding-top: 120px;">
            @if (session('status'))
                <div id="flash-notification" class="fixed top-24 right-4 z-50 max-w-sm w-full animate-fade-in-down">
                    <div class="rounded-lg border border-brand-100 bg-white p-4 shadow-lg flex items-center gap-3">
                        <div class="text-brand-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div id="flash-errors" class="fixed top-24 right-4 z-50 max-w-sm w-full animate-fade-in-down" style="top: {{ session('status') ? '8rem' : '6rem' }}">
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 shadow-lg flex items-start gap-3">
                        <div class="text-red-500 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-red-800">Please fix the following problems:</p>
                            <ul class="list-disc ml-5 mt-1 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        @php
            $supportPhone = '923122294445';
            $supportText = 'Assalam o Alaikum. I am contacting you from this page: ' . request()->fullUrl();
            $supportWhatsappUrl = 'https://api.whatsapp.com/send/?phone=' . $supportPhone . '&text=' . urlencode($supportText) . '&type=phone_number&app_absent=0';
        @endphp
        <a href="{{ $supportWhatsappUrl }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Support"
            style="position: fixed; bottom: 24px; right: 24px; z-index: 60; display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 9999px; background-color: #25D366;"
            class="h-14 w-14 rounded-full text-white shadow-lg shadow-emerald-500/30 transition-transform hover:-translate-y-1">
            <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
        </a>

        <!-- AdSense Ad - Before Footer -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-2">
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

        <!-- Footer (Shaadi Style: Darker/Textured) -->
        <x-public-footer />
    </div>
    @include('components.match-animation')
    <script>
        // Auto-hide flash notifications
        document.addEventListener('DOMContentLoaded', function () {
            const notification = document.getElementById('flash-notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.transition = 'opacity 0.5s ease-out';
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>
