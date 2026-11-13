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

    <style>
        .login-bg-overlay {
            background-color: rgba(15, 23, 42, 0.6) !important;
            backdrop-filter: blur(2px) !important;
        }

        .login-card-glass {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(8px) !important;
            max-width: 450px !important;
            width: 100% !important;
            margin: auto !important;
        }

        .login-container {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 100vh !important;
            padding: 2rem 1rem !important;
        }
    </style>
</head>

<body class="font-sans text-slate-900 antialiased m-0 p-0">
    <div class="min-h-screen bg-gray-50">
        <x-public-header />

        <main style="padding-top: 120px; min-height: 80vh;" class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                <!-- Branding in Card -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-block">
                        <span class="text-3xl font-bold tracking-tight font-sans" style="color: #881337;">IDEAL MARRIAGE CENTER</span>
                    </a>
                </div>

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
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
</body>

</html>
