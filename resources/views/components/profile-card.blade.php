@props(['profile'])

@php
    $completionScore = max(0, min(100, (int) ($profile->completion_score ?? 0)));
    $heightCm = is_numeric($profile->height_cm ?? null) ? (float) $profile->height_cm : null;
    $heightText = '-';
    if ($heightCm) {
        $totalInches = (int) round($heightCm / 2.54);
        $heightText = intdiv($totalInches, 12) . '′ ' . ($totalInches % 12) . '″';
    }
    $firstName = trim(
        \App\Support\DisplayValue::text($profile->first_name ?? null, '')
        . ' '
        . \App\Support\DisplayValue::text($profile->last_name ?? null, '')
    );
    $profileName = $firstName !== '' ? $firstName : 'Available Profile';
    $religionText = \App\Support\DisplayValue::text($profile->religion ?? null, '-');
    $casteText = \App\Support\DisplayValue::text($profile->caste ?? null, '');
    $sectText = \App\Support\DisplayValue::text($profile->sect ?? null, '');
    $motherTongueText = \App\Support\DisplayValue::text($profile->mother_tongue ?? null, '-');
    $maritalStatusText = \App\Support\DisplayValue::text($profile->marital_status ?? null, '-');
    $countryText = \App\Support\DisplayValue::text($profile->country ?? null, '');
    $cityText = \App\Support\DisplayValue::text($profile->city ?? null, '-');
    $educationText = \App\Support\DisplayValue::text($profile->education ?? null, '');
    $professionText = \App\Support\DisplayValue::text($profile->profession ?? null, '');
    $locationText = $cityText . ($countryText !== '' && $countryText !== '-' ? ', ' . $countryText : '');
    $communityText = $religionText . ($sectText !== '' ? ' · ' . $sectText : '') . ($casteText !== '' ? ' · ' . $casteText : '');
    $careerText = collect([$professionText, $educationText])->filter()->implode(' · ');
    $planName = $profile->user?->activeSubscription?->plan?->name;
    $isAdminOwnedProfile = $profile->user?->isAdmin() ?? false;
    $matchRaw = data_get($profile, 'match_percentage');
    $matchPercent = is_numeric($matchRaw) ? max(0, min(100, (int) round($matchRaw))) : null;
@endphp

@once
    <style>
        .idm-card { position: relative; overflow: hidden; margin-bottom: 16px; border: 1px solid #e4dce0; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px rgba(55, 32, 41, .045); font-family: "Manrope", sans-serif; transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
        .idm-card:hover { border-color: #d8c3cb; box-shadow: 0 10px 26px rgba(68, 35, 47, .08); transform: translateY(-1px); }
        .idm-row { display: flex; flex-direction: column; }
        .idm-photo-col { position: relative; width: 100%; flex-shrink: 0; background: #f1ebed; }
        .idm-photo { position: relative; width: 100%; height: 240px; overflow: hidden; background: #eee8eb; }
        .idm-photo img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: top center; }
        .idm-photo-placeholder { display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 9px; width: 100%; height: 100%; color: #aa969f; font-size: 10px; font-weight: 700; }
        .idm-photo-placeholder svg { width: 48px; height: 48px; color: #c6b5bc; }
        .idm-status { position: absolute; bottom: 11px; left: 11px; display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px; border: 1px solid rgba(255,255,255,.65); border-radius: 999px; background: rgba(255,255,255,.92); color: #61535a; font-size: 9px; font-weight: 800; box-shadow: 0 3px 10px rgba(34,20,26,.1); backdrop-filter: blur(6px); }
        .idm-dot { width: 7px; height: 7px; border-radius: 50%; background: #aa9ba2; }
        .idm-dot.is-online { background: #20a45a; box-shadow: 0 0 0 2px rgba(32,164,90,.13); }
        .idm-body { display: flex; flex: 1; min-width: 0; flex-direction: column; padding: 17px 18px 16px; }
        .idm-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .idm-identity { min-width: 0; }
        .idm-name-row { display: flex; align-items: center; flex-wrap: wrap; gap: 7px; }
        .idm-name { margin: 0; overflow: hidden; color: #8f2946; font-family: "Playfair Display", serif; font-size: 24px; line-height: 1.2; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
        .idm-ver { display: inline-flex; align-items: center; gap: 3px; padding: 3px 6px; border-radius: 999px; background: #edf8f1; color: #25754a; font-size: 8px; line-height: 1; font-weight: 800; letter-spacing: .03em; text-transform: uppercase; white-space: nowrap; }
        .idm-ver svg { width: 10px; height: 10px; }
        .idm-id { margin: 5px 0 0; color: #9b8d94; font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .idm-plan { flex-shrink: 0; padding: 5px 8px; border: 1px solid #ead8b8; border-radius: 7px; background: #fffaf0; color: #916826; font-size: 9px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .idm-career { min-height: 18px; margin: 8px 0 0; overflow: hidden; color: #685b62; font-size: 12px; line-height: 1.5; text-overflow: ellipsis; white-space: nowrap; }
        .idm-match { display: inline-flex; align-items: center; gap: 4px; margin-left: 2px; padding: 3px 7px; border: 1px solid #dfd1e8; border-radius: 999px; background: #faf7fd; color: #75528e; font-size: 8px; font-weight: 800; white-space: nowrap; }
        .idm-match svg { width: 10px; height: 10px; }
        .idm-details { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); margin-top: 14px; overflow: hidden; border: 1px solid #ece5e8; border-radius: 9px; background: #fdfcfc; }
        .idm-detail { min-width: 0; padding: 10px 11px; border-right: 1px solid #ece5e8; border-bottom: 1px solid #ece5e8; }
        .idm-detail:nth-child(3n) { border-right: 0; }
        .idm-detail:nth-last-child(-n+3) { border-bottom: 0; }
        .idm-detail-label { display: block; margin-bottom: 4px; color: #a09198; font-size: 9px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .idm-detail-value { display: block; overflow: hidden; color: #4a3e44; font-size: 11px; font-weight: 700; line-height: 1.35; text-overflow: ellipsis; white-space: nowrap; }
        .idm-footer { display: flex; align-items: center; gap: 20px; margin-top: auto; padding-top: 14px; }
        .idm-completion { flex: 1; min-width: 0; }
        .idm-completion-label { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px; color: #92848b; font-size: 8px; font-weight: 700; }
        .idm-completion-label strong { color: #4f7e62; font-size: 9px; }
        .idm-progress-track { height: 5px; overflow: hidden; border-radius: 999px; background: #e9e4e6; }
        .idm-progress-fill { height: 100%; border-radius: inherit; background: #57a376; }
        .idm-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-width: 137px; height: 39px; padding: 0 15px; border-radius: 8px; background: #9d2b48; color: #fff; font-size: 10px; font-weight: 800; text-decoration: none; box-shadow: 0 4px 11px rgba(157,43,72,.15); transition: background .16s ease; }
        .idm-btn:hover { background: #862139; color: #fff; }
        .idm-btn svg { width: 13px; height: 13px; }
        @media (min-width: 768px) {
            .idm-row { flex-direction: row; }
            .idm-photo-col { width: 194px; }
            .idm-photo { height: 100%; min-height: 257px; }
        }
        @media (max-width: 767px) {
            .idm-body { padding: 16px; }
            .idm-name { font-size: 23px; }
            .idm-heading { gap: 8px; }
            .idm-details { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .idm-detail:nth-child(3n) { border-right: 1px solid #ece5e8; }
            .idm-detail:nth-child(2n) { border-right: 0; }
            .idm-detail:nth-last-child(-n+3) { border-bottom: 1px solid #ece5e8; }
            .idm-detail:nth-last-child(-n+2) { border-bottom: 0; }
            .idm-footer { gap: 14px; }
        }
        @media (max-width: 420px) {
            .idm-photo { height: 225px; }
            .idm-plan { display: none; }
            .idm-footer { align-items: stretch; flex-direction: column; }
            .idm-btn { width: 100%; }
        }
    </style>
@endonce

@unless($isAdminOwnedProfile)
    <article class="idm-card">
        <div class="idm-row">
            <div class="idm-photo-col">
                <div class="idm-photo">
                    @if($profile->primaryPhoto)
                        <img src="{{ \App\Support\Media::thumb($profile->primaryPhoto->path, 400) }}" alt="{{ $profileName }}" loading="lazy" decoding="async">
                    @else
                        <div class="idm-photo-placeholder">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0"/></svg>
                            <span>Photo not available</span>
                        </div>
                    @endif
                    <span class="idm-status">
                        <span class="idm-dot {{ $profile->isOnline() ? 'is-online' : '' }}"></span>
                        {{ $profile->isOnline() ? 'Online now' : 'Away' }}
                    </span>
                </div>
            </div>

            <div class="idm-body">
                <div class="idm-heading">
                    <div class="idm-identity">
                        <div class="idm-name-row">
                            <h3 class="idm-name">{{ $profileName }}</h3>
                            @if($profile->cnic_verified)
                                <span class="idm-ver">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 13l4 4L19 7"/></svg>
                                    Verified
                                </span>
                            @endif
                            @if(!is_null($matchPercent) && $matchPercent > 0)
                                <span class="idm-match" title="Based on your partner preferences">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke-width="1.8"/><circle cx="12" cy="12" r="3.5" stroke-width="1.8"/></svg>
                                    {{ $matchPercent }}% match
                                </span>
                            @endif
                        </div>
                        <p class="idm-id">Profile ID: {{ $profile->display_id }}</p>
                    </div>
                    @if($planName)
                        <span class="idm-plan">{{ $planName }}</span>
                    @endif
                </div>

                <p class="idm-career" title="{{ $careerText }}">{{ $careerText !== '' ? $careerText : 'Profile details available on request' }}</p>

                <div class="idm-details">
                    <div class="idm-detail">
                        <span class="idm-detail-label">Age</span>
                        <span class="idm-detail-value">{{ $profile->age ? $profile->age . ' years' : '-' }}</span>
                    </div>
                    <div class="idm-detail">
                        <span class="idm-detail-label">Height</span>
                        <span class="idm-detail-value">{{ $heightText }}</span>
                    </div>
                    <div class="idm-detail">
                        <span class="idm-detail-label">Marital status</span>
                        <span class="idm-detail-value">{{ $maritalStatusText }}</span>
                    </div>
                    <div class="idm-detail">
                        <span class="idm-detail-label">Location</span>
                        <span class="idm-detail-value" title="{{ $locationText }}">{{ $locationText }}</span>
                    </div>
                    <div class="idm-detail">
                        <span class="idm-detail-label">Religion / Community</span>
                        <span class="idm-detail-value" title="{{ $communityText }}">{{ $communityText }}</span>
                    </div>
                    <div class="idm-detail">
                        <span class="idm-detail-label">Mother tongue</span>
                        <span class="idm-detail-value">{{ $motherTongueText }}</span>
                    </div>
                </div>

                <div class="idm-footer">
                    <div class="idm-completion">
                        <div class="idm-completion-label"><span>Profile completion</span><strong>{{ $completionScore }}%</strong></div>
                        <div class="idm-progress-track"><div class="idm-progress-fill" style="width: {{ $completionScore }}%;"></div></div>
                    </div>
                    <a href="{{ route('profiles.show', $profile->display_id) }}" class="idm-btn">
                        View profile
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-5-5l5 5-5 5"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </article>
@endunless
