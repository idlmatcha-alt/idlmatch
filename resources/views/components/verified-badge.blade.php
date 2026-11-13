@props(['class' => 'h-8'])

@php
    $uniqueId = \Illuminate\Support\Str::random(8);
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center ' . $class]) }} title="CNIC Verified">
    <svg viewBox="0 0 160 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-full w-auto">
        <defs>
            <linearGradient id="shieldGradient-{{ $uniqueId }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#4CAF50"/>
                <stop offset="100%" stop-color="#1B5E20"/>
            </linearGradient>
            <linearGradient id="shieldBorder-{{ $uniqueId }}" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#C6FF00"/>
                <stop offset="100%" stop-color="#64DD17"/>
            </linearGradient>
            <linearGradient id="checkGradient-{{ $uniqueId }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#FFF176"/>
                <stop offset="100%" stop-color="#FDD835"/>
            </linearGradient>
            <linearGradient id="bannerGradient-{{ $uniqueId }}" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#2E7D32"/>
                <stop offset="100%" stop-color="#1B5E20"/>
            </linearGradient>
            <filter id="dropShadow-{{ $uniqueId }}" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur in="SourceAlpha" stdDeviation="1"/>
                <feOffset dx="1" dy="1" result="offsetblur"/>
                <feMerge>
                    <feMergeNode in="offsetblur"/>
                    <feMergeNode in="SourceGraphic"/>
                </feMerge>
            </filter>
        </defs>

        <!-- Banner -->
        <path d="M25 8 H150 C154 8 158 12 158 16 V24 C158 28 154 32 150 32 H25 V8 Z" fill="url(#bannerGradient-{{ $uniqueId }})" stroke="#64DD17" stroke-width="1" filter="url(#dropShadow-{{ $uniqueId }})"/>
        
        <!-- Text -->
        <text x="92" y="22" fill="white" font-family="Arial, sans-serif" font-weight="900" font-size="13" text-anchor="middle" dominant-baseline="central" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5); letter-spacing: 0.5px;">CNIC VERIFIED</text>

        <!-- Shield -->
        <path d="M2 5 L20 0 L38 5 V15 C38 26 31 34 20 38 C9 34 2 26 2 15 V5 Z" fill="url(#shieldGradient-{{ $uniqueId }})" stroke="url(#shieldBorder-{{ $uniqueId }})" stroke-width="2" filter="url(#dropShadow-{{ $uniqueId }})"/>
        
        <!-- Checkmark -->
        <path d="M12 18 L18 24 L30 10" fill="none" stroke="url(#checkGradient-{{ $uniqueId }})" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" filter="url(#dropShadow-{{ $uniqueId }})"/>
    </svg>
</div>