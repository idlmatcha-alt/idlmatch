@php
    $record = $getRecord();
    $profile = $record?->profile;
    $galleryPhotos = $profile?->photos?->where('is_primary', false)->values() ?? collect();
    $visibleGalleryPhotos = $galleryPhotos->take(5);
@endphp

@if(!$profile)
    <span style="color:#94a3b8; font-size:12px;">No profile</span>
@elseif($visibleGalleryPhotos->isEmpty())
    <span style="color:#94a3b8; font-size:12px;">No gallery photos</span>
@else
    <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
        @foreach($visibleGalleryPhotos as $photo)
            <img
                src="{{ asset('storage/' . ltrim($photo->path, '/')) }}"
                alt="Gallery"
                style="width:42px; height:42px; border-radius:8px; object-fit:cover; border:1px solid #cbd5e1;" />
        @endforeach

        @if($galleryPhotos->count() > $visibleGalleryPhotos->count())
            <span style="font-size:11px; font-weight:700; color:#475569;">
                +{{ $galleryPhotos->count() - $visibleGalleryPhotos->count() }}
            </span>
        @endif
    </div>
@endif
