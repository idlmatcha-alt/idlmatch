@php
    $profilePhotos = $profile?->photos ?? collect();
    $mainPhoto = $profilePhotos->firstWhere('is_primary', true);
    $galleryPhotos = $profilePhotos->where('is_primary', false)->values();
@endphp

<div style="display:flex; flex-direction:column; gap:14px;">
    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px;">
        <div style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.06em;">User ID</div>
        <div style="margin-top:4px; font-size:18px; font-weight:800; color:#0f172a;">
            {{ $profile?->display_id ?? 'N/A' }}
        </div>
    </div>

    <div>
        <div style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px;">Profile Photo</div>
        @if($mainPhoto)
            <img
                src="{{ asset('storage/' . ltrim($mainPhoto->path, '/')) }}"
                alt="Main Photo"
                style="width:120px; height:120px; border-radius:12px; object-fit:cover; border:1px solid #cbd5e1;" />
        @else
            <div style="font-size:13px; color:#94a3b8;">No profile photo</div>
        @endif
    </div>

    <div>
        <div style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px;">Gallery Photos</div>
        @if($galleryPhotos->isEmpty())
            <div style="font-size:13px; color:#94a3b8;">No gallery photos</div>
        @else
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                @foreach($galleryPhotos as $photo)
                    <img
                        src="{{ asset('storage/' . ltrim($photo->path, '/')) }}"
                        alt="Gallery Photo"
                        style="width:72px; height:72px; border-radius:10px; object-fit:cover; border:1px solid #cbd5e1;" />
                @endforeach
            </div>
        @endif
    </div>
</div>
