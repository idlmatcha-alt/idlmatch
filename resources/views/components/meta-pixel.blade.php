@php
    $pixelId = (string) config('analytics.meta_pixel_id', '');
    $testCode = (string) config('analytics.meta_pixel_test_code', '');
@endphp

@if($pixelId !== '')
    <!-- Meta Pixel (idlmatch.com) -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return; n = f.fbq = function () {
                n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
            t = b.createElement(e); t.async = !0; t.src = v;
            s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s)
        }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

        @if($testCode !== '')
        fbq('init', @json($pixelId), {}, {testEventCode: @json($testCode)});
        @else
        fbq('init', @json($pixelId));
        @endif

        fbq('track', 'PageView');
        @foreach(\App\Support\Tracking::consumeMeta() as $event)
        fbq('track', @json($event['event']), @json((object) $event['params']));
        @endforeach
    </script>
    <noscript>
        <img height="1" width="1" style="display: none" alt=""
            src="https://www.facebook.com/tr?id={{ urlencode($pixelId) }}&ev=PageView&noscript=1">
    </noscript>
@endif
