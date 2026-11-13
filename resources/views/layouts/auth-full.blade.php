<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

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

<body class="font-sans text-slate-900 antialiased h-full">
    @if ($chrome ?? false)
        <x-public-header />
    @endif

    <div class="min-h-full flex flex-col">
        <div class="flex-1" @if ($chrome ?? false) style="padding-top: 120px;" @endif>
            {{ $slot }}
        </div>
        @if ($chrome ?? false)
            <x-public-footer />
        @else
            <footer class="border-t border-slate-100 bg-white">
                <div class="section py-6">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} Ideal Marriage Center. All rights reserved. <x-credit />
                    </p>
                </div>
            </footer>
        @endif
    </div>
</body>

</html>
