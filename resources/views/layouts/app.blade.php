<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Code from Magzel.com | For support contact hellomagzel@gmail.com -->

    <x-seo-meta />

    <!-- Impact is a system font, no external loading needed -->

    <!-- Scripts -->
    <x-frontend-assets />

    <x-meta-pixel />

    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7126798303930967"
         crossorigin="anonymous"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <x-public-header />

        <!-- Page Content -->
        <main style="padding-top: 120px; min-height: 80vh;">
            <!-- Page Heading -->
            @if (isset($header) || View::hasSection('header'))
                <header class="bg-white shadow-sm mb-6">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header ?? '' }}
                        @yield('header')
                    </div>
                </header>
            @endif

            @if (session('status'))
                <div id="flash-status" class="section pt-6">
                    <div class="rounded-2xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                        {{ session('status') }}
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div id="flash-errors" class="section pt-4">
                    <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
        
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

        <x-public-footer />
    </div>
    <x-whatsapp-button />
    @include('components.match-animation')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hideElement = (id) => {
                const el = document.getElementById(id);
                if (el) {
                    setTimeout(() => {
                        el.style.transition = 'opacity 0.5s ease-out';
                        el.style.opacity = '0';
                        setTimeout(() => el.remove(), 500);
                    }, 5000);
                }
            };
            hideElement('flash-status');
            hideElement('flash-errors');

            // Clear registration draft if it exists
            if (localStorage.getItem('idlmatch_registration_draft')) {
                localStorage.removeItem('idlmatch_registration_draft');
            }
        });
    </script>
</body>

</html>
