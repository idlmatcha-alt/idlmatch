@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = null;

    if (is_file($manifestPath)) {
        $decoded = json_decode((string) file_get_contents($manifestPath), true);
        if (is_array($decoded)) {
            $manifest = $decoded;
        }
    }

    $jsEntry = $manifest['resources/js/app.js'] ?? null;
    $cssFiles = [];
    $jsFile = null;

    if (is_array($jsEntry) && !empty($jsEntry['file'])) {
        $jsFile = (string) $jsEntry['file'];

        if (!empty($jsEntry['css']) && is_array($jsEntry['css'])) {
            foreach ($jsEntry['css'] as $cssFile) {
                $cssFiles[] = (string) $cssFile;
            }
        }
    }

    $cssEntry = $manifest['resources/css/app.css'] ?? null;
    if (is_array($cssEntry) && !empty($cssEntry['file'])) {
        $cssFiles[] = (string) $cssEntry['file'];
    }

    $cssFiles = array_values(array_unique(array_filter($cssFiles)));

    $fallbackCss = public_path('static/app.css');
    $fallbackJs = public_path('static/app.js');
@endphp

@if($jsFile !== null)
    @foreach($cssFiles as $cssFile)
        <link rel="stylesheet" href="{{ asset('build/' . ltrim($cssFile, '/')) }}">
    @endforeach
    <script type="module" src="{{ asset('build/' . ltrim($jsFile, '/')) }}"></script>
@elseif(is_file($fallbackCss) && is_file($fallbackJs))
    <link rel="stylesheet" href="{{ asset('static/app.css') }}">
    <script type="module" src="{{ asset('static/app.js') }}"></script>
@else
    {{-- Final fallback to Vite when static assets are unavailable. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
<link rel="stylesheet" href="{{ asset('static/about.css') }}">
