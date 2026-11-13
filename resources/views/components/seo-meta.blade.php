@php
    use App\Support\Seo;

    $seo = Seo::forRequest(request());
    $site = config('seo.site');
    $jsonld = Seo::jsonLd(request(), $seo);
    $keys = implode(', ', array_slice($seo['keys'], 0, 20));
@endphp
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['desc'] }}">
<meta name="keywords" content="{{ $keys }}">
<meta name="robots" content="{{ $seo['robots'] }}">
<meta name="language" content="English">
<meta name="theme-color" content="{{ $site['theme_color'] }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">
<link rel="home" href="{{ rtrim(Seo::baseUrl(), '/') }}/">
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="sitemap" type="application/xml" href="{{ rtrim(Seo::baseUrl(), '/') }}/sitemap.xml">

<meta property="og:type" content="{{ $seo['type'] === 'article' ? 'article' : 'website' }}">
<meta property="og:site_name" content="{{ $site['name'] }}">
<meta property="og:title" content="{{ $seo['bare_title'] }}">
<meta property="og:description" content="{{ $seo['desc'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:image:width" content="{{ $site['og_image_width'] }}">
<meta property="og:image:height" content="{{ $site['og_image_height'] }}">
<meta property="og:image:alt" content="{{ $seo['image_alt'] }}">
<meta property="og:locale" content="{{ $site['locale'] }}">
@if($site['facebook'])
    <meta property="og:see_also" content="{{ $site['facebook'] }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="{{ $site['twitter'] }}">
<meta name="twitter:title" content="{{ $seo['bare_title'] }}">
<meta name="twitter:description" content="{{ $seo['desc'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">
<meta name="twitter:image:alt" content="{{ $seo['image_alt'] }}">

@if ($googleVerify = config('seo.site.google_site_verification'))
    <meta name="google-site-verification" content="{{ $googleVerify }}">
@endif

@if ($bingVerify = config('seo.site.bing_site_verification'))
    <meta name="msvalidate.01" content="{{ $bingVerify }}">
@endif

<script type="application/ld+json">{!! json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
