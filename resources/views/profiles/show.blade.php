@extends('layouts.marketing')



@section('content')
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg shadow-sm">
                <p class="font-bold mb-1">Please fix the following errors:</p>
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @php
        $isOwner = Auth::check() && (int) Auth::id() === (int) $profile->user_id;
        $profileUrl = route('profiles.show', $profile->display_id);
        $profileSupportPhone = '923122294445';
        $viewerProfileUrl = null;
        if (Auth::check() && Auth::user()?->profile?->display_id) {
            $viewerProfileUrl = route('profiles.show', Auth::user()->profile->display_id);
        }

        if (Auth::check()) {
            $profileWhatsappText = 'Assalam o Alaikum. I am interested in this profile.'
                . "\n" . 'Interested profile link: ' . $profileUrl
                . ($viewerProfileUrl ? "\n" . 'My profile link: ' . $viewerProfileUrl : '');
        } else {
            $profileWhatsappText = 'Assalam o Alaikum. Guest client is interested in this profile.'
                . "\n" . 'Interested profile link: ' . $profileUrl;
        }

        $profileWhatsappUrl = 'https://api.whatsapp.com/send/?phone=' . $profileSupportPhone . '&text=' . urlencode($profileWhatsappText) . '&type=phone_number&app_absent=0';
        $galleryLimit = 4;
        $galleryPhotos = $profile->photos->where('is_primary', false)->values();
        $galleryIsFull = $galleryPhotos->count() >= $galleryLimit;
        $gallerySlides = $galleryPhotos->map(function ($galleryPhoto) use ($profile) {
            return [
                'url' => asset('storage/' . $galleryPhoto->path),
                'alt' => ($profile->first_name ?: $profile->display_id) . ' gallery photo',
            ];
        })->values();
        $galleryUploadUrl = $mobileGalleryUploadUrl ?? null;

        $heightText = 'N/A';
        if (!empty($profile->height_cm)) {
            $feet = floor($profile->height_cm / 30.48);
            $inches = round(($profile->height_cm / 2.54) - ($feet * 12));
            $heightText = $feet . "'" . $inches . '" (' . $profile->height_cm . ' cm)';
        }

        $weightText = $profile->weight_kg ?? 'N/A';
        $livingArrangementMap = [
            'own' => 'Own House',
            'rented' => 'Rented',
            'sharing' => 'Live With Family',
        ];
        $livingArrangement = $profile->home_status
            ? ($livingArrangementMap[$profile->home_status] ?? ucfirst($profile->home_status))
            : 'N/A';

        $originCountry = $profile->origin_country ?? $profile->country ?? 'N/A';
        $originState = $profile->origin_state ?? $profile->state ?? 'N/A';
        $originCity = $profile->origin_city ?? $profile->home_town ?? $profile->city ?? 'N/A';

        $willingToRelocate = $profile->willing_to_relocate ?? 'N/A';
        $lookingToMarry = $profile->looking_to_marry ?? 'N/A';
        $religiousness = $profile->religiousness ?? 'N/A';
        $isRevertText = $profile->is_revert === null ? 'N/A' : ($profile->is_revert ? 'Yes' : 'No');

        $jobPost = $profile->nature_of_job ?? $profile->job_type ?? 'N/A';

        $pref = $profile->preferences;
        $prefAge = 'Any';
        if ($pref && ($pref->desired_age_min || $pref->desired_age_max)) {
            $prefAge = ($pref->desired_age_min ?? '18') . ' - ' . ($pref->desired_age_max ?? '70');
        }

        $prefHeight = 'Any';
        if ($pref && ($pref->desired_height_min || $pref->desired_height_max)) {
            $min = $pref->desired_height_min ? $pref->desired_height_min . ' cm' : 'Any';
            $max = $pref->desired_height_max ? $pref->desired_height_max . ' cm' : 'Any';
            $prefHeight = $min . ' - ' . $max;
        }

        $canMessage = Auth::check() && Auth::user()->canMessage();
        $isPaidMember = Auth::check() && Auth::user()->canViewContacts();
        $prefText = static function (...$values): string {
            return \App\Support\DisplayValue::text($values, 'Any');
        };

        $prefCountry = $prefText(data_get($pref, 'desired_country'), data_get($pref, 'desired_countries'));
        $prefState = $prefText(data_get($pref, 'desired_state'));
        $prefCity = $prefText(data_get($pref, 'desired_city'), data_get($pref, 'desired_cities'));
        $prefTongue = $prefText(data_get($pref, 'desired_mother_tongue'));
        $prefReligion = $prefText(data_get($pref, 'desired_religion'));
        $prefSect = $prefText(data_get($pref, 'desired_sect'), data_get($pref, 'desired_sects'));
        $prefReligiousness = $prefText(data_get($pref, 'desired_religiousness'));
        $prefEducation = $prefText(data_get($pref, 'desired_education'));
        $prefProfession = $prefText(data_get($pref, 'desired_profession'), data_get($pref, 'desired_professions'));
        $prefMaritalStatus = $prefText(data_get($pref, 'desired_marital_status'), data_get($pref, 'desired_marital_statuses'));
        $prefCaste = $prefText(data_get($pref, 'desired_caste'));
        $prefRelocate = $prefText(data_get($pref, 'desired_willing_to_relocate'));
        $prefLivingArrangement = $prefText(data_get($pref, 'desired_living_arrangement'));
        $prefIncome = $prefText(data_get($pref, 'desired_income'));
        $prefDisability = $prefText(data_get($pref, 'desired_disability'));

        $personalRows = [
            ['Gender', ucfirst($profile->gender)],
            ['Age', $profile->age ? $profile->age . ' Years' : 'N/A'],
            ['Country', $profile->country ?? 'N/A'],
            ['State', $profile->state ?? 'N/A'],
            ['City', $profile->city ?? 'N/A'],
            ['Marital Status', $profile->marital_status ?? 'N/A'],
            ['Mother Tongue', $profile->mother_tongue ?? 'N/A'],
            ['Caste', $profile->caste ?? 'N/A'],
            ['Country of Origin', $originCountry],
            ['State of Origin', $originState],
            ['City of Origin', $originCity],
            ['Willing to Relocate', $willingToRelocate],
            ['Looking to Marry', $lookingToMarry],
            ['Looking to Marry', $lookingToMarry],
            ['Height', $heightText],
            ['Weight (KG)', $weightText],
            ['Disability', $profile->disability ?? 'None'],
        ];

        $propertyRows = [
            ['Home Status', ucfirst($profile->home_status ?? 'N/A')],
            ['Home Size', $profile->home_size ?? 'N/A'],
            ['Other Properties', $profile->other_properties ?? 'N/A'],
        ];

        $careerRows = [
            ['Qualification', $profile->education ?? 'N/A'],
            ['University', $profile->university ?? 'N/A'],
            ['Occupation', $profile->profession ?? 'N/A'],
            ['Job Post', $jobPost],
            ['Monthly Income', $profile->income ?? 'N/A'],
        ];

        $religionRows = [
            ['Religion', $profile->religion ?? 'N/A'],
            ['Sect', $profile->sect ?? 'N/A'],
            ['Are You Revert', $isRevertText],
        ];

        $brothersCount = max(0, (int) ($profile->brothers_count ?? 0));
        $sistersCount = max(0, (int) ($profile->sisters_count ?? 0));
        $marriedBrothersCount = max(0, (int) ($profile->married_brothers_count ?? 0));
        $marriedSistersCount = max(0, (int) ($profile->married_sisters_count ?? 0));

        if ($marriedBrothersCount === 0 && $marriedSistersCount === 0) {
            $marriedSiblingsCount = max(0, (int) ($profile->married_siblings_count ?? 0));
            $marriedBrothersCount = min($brothersCount, $marriedSiblingsCount);
            $marriedSistersCount = max(0, $marriedSiblingsCount - $marriedBrothersCount);
        }

        $marriedSiblingsCount = $marriedBrothersCount + $marriedSistersCount;

        $familyRows = [
            ['Brothers', $brothersCount],
            ['Married Brothers', $marriedBrothersCount],
            ['Sisters', $sistersCount],
            ['Married Sisters', $marriedSistersCount],
            ['Father Name', $profile->father_name ?? 'N/A'],
            ['Father Occupation', $profile->father_occupation ?? 'N/A'],
            ['Mother Occupation', $profile->mother_occupation ?? 'N/A'],
            ['Married Siblings (Total)', $marriedSiblingsCount],
        ];

        $preferenceRows = [
            ['Gender', $profile->gender == 'male' ? 'Female' : 'Male'],
            ['Age', $prefAge],
            ['Country', $prefCountry],
            ['State', $prefState],
            ['City', $prefCity],
            ['Tongue', $prefTongue],
            ['Religiousness', $prefReligiousness],
            ['Religion', $prefReligion],
            ['Sect', $prefSect],
            ['Qualification', $prefEducation],
            ['Profession', $prefProfession],
            ['Willing to Relocate', $prefRelocate],
            ['Income', $prefIncome],
            ['Marital Status', $prefMaritalStatus],
            ['Living Arrangements', $prefLivingArrangement],
            ['Heights', $prefHeight],
            ['Disabilities', $prefDisability],
            ['Caste', $prefCaste],
        ];
    @endphp

    <style>
        [x-cloak] { display: none !important; }
        .profile-label {
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
        }

        .profile-value {
            font-size: 14px;
            color: #0f172a;
            font-weight: 600;
        }

        .profile-section-title {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .chat-cta-btn {
            animation: chatPulse 2.1s ease-in-out infinite, chatGlow 2.8s ease-in-out infinite;
            transition: transform 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
            box-shadow: 0 10px 22px rgba(22, 163, 74, 0.28);
        }

        .chat-cta-btn:hover {
            animation-play-state: paused;
            transform: translateY(-1px) scale(1.03);
            box-shadow: 0 14px 30px rgba(22, 163, 74, 0.36);
        }

        .related-match-card {
            border: 1px solid #dbe3ec;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            padding: 12px 14px;
            overflow: hidden;
            height: 100%;
        }

        .related-match-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-height: 0;
        }

        .related-match-avatar {
            position: relative;
            width: 112px;
            min-width: 112px;
            height: 112px;
            border-radius: 9999px;
            overflow: hidden;
            border: 2px solid #be185d;
            background: #f1f5f9;
            flex-shrink: 0;
        }

        .related-match-avatar img {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .related-match-content {
            min-width: 0;
            flex: 1;
        }

        .related-match-name {
            margin: 0;
            color: #b91c1c;
            font-size: clamp(1.6rem, 2.1vw, 2.1rem);
            line-height: 1.05;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .related-match-meta {
            margin-top: 6px;
            display: grid;
            gap: 4px;
        }

        .related-match-meta-row {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #1f2937;
            font-size: 13px;
        }

        .related-match-meta-row svg {
            color: #9ca3af;
            flex-shrink: 0;
        }

        .related-match-btn {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(180deg, #40b6ec 0%, #2a93d9 100%);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.2);
            transition: transform 120ms ease, filter 120ms ease;
        }

        .related-match-btn:hover {
            filter: brightness(0.95);
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
            .related-match-avatar {
                width: 94px;
                min-width: 94px;
                height: 94px;
            }

            .related-match-name {
                font-size: 1.5rem;
            }

            .related-match-card {
                padding: 12px;
            }

            .related-match-row {
                gap: 10px;
            }
        }

        @keyframes chatPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.06);
            }
        }

        @keyframes chatGlow {
            0%, 100% {
                box-shadow: 0 10px 22px rgba(22, 163, 74, 0.25);
            }
            50% {
                box-shadow: 0 14px 30px rgba(22, 163, 74, 0.4);
            }
        }
    </style>

    <div class="min-h-screen" style="background-color: #f6f6f8;">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid lg:grid-cols-3 gap-6">
                <aside class="lg:col-span-1 space-y-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4" x-data>
                            <div class="aspect-square bg-slate-100 rounded-xl overflow-hidden relative group">
                                @if($profile->primaryPhoto)
                                    <img src="{{ \App\Support\Media::thumb($profile->primaryPhoto->path, 700) }}" alt="Matrimonial biodata photo {{ $profile->display_id }}"
                                        class="w-full h-full object-cover" decoding="async">
                                @else
                                    <img src="{{ asset('images/avatar-placeholder.svg') }}" alt="Photo not available for biodata {{ $profile->display_id }}"
                                        class="w-full h-full object-cover" decoding="async" width="400" height="400">
                                @endif

                                @if($isOwner)
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                        <div class="flex items-center gap-2">
                                            <button @click="$refs.photoInput.click()" class="bg-white text-gray-900 rounded-full p-3 hover:bg-gray-100 transition shadow-lg transform hover:scale-105" title="Change Main Photo">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>

                                            @if($profile->primaryPhoto)
                                                <form method="POST" action="{{ route('photos.destroy', $profile->primaryPhoto) }}"
                                                    onsubmit="return confirm('Delete main photo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 text-white rounded-full p-3 hover:bg-red-700 transition shadow-lg transform hover:scale-105" title="Delete Main Photo">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <form action="{{ route('profile.update-section', 'photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                                        @csrf
                                        <input type="file" name="photo" x-ref="photoInput" @change="$el.form.submit()" accept="image/*">
                                    </form>
                                @endif
                            </div>

                            @if($isOwner)
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <button type="button" @click="$refs.photoInput.click()"
                                        class="w-full py-2 rounded-lg text-xs font-bold border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                                        Change Main Photo
                                    </button>
                                    @if($profile->primaryPhoto)
                                        <form method="POST" action="{{ route('photos.destroy', $profile->primaryPhoto) }}"
                                            onsubmit="return confirm('Delete main photo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full py-2 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition">
                                                Delete Main Photo
                                            </button>
                                        </form>
                                    @else
                                        <div></div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="px-4 pb-4 text-center border-b border-slate-200">
                            <div class="flex items-center justify-center gap-2">
                                <h1 class="text-xl font-bold" style="color: #b91c1c;">
                                    {{ trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')) ?: $profile->display_id }}
                                </h1>
                                @if($profile->cnic_verified || in_array($profile->verification_status, ['verified', 'approved'], true))
                                    <x-verified-badge class="h-7" />
                                @endif
                            </div>
                            <p class="text-sm text-slate-500 mt-1">{{ $profile->profession ?? 'Not specified' }}</p>
                            <p class="text-xs text-slate-400 mt-1">Profile ID: {{ $profile->display_id }}</p>

                            <div class="flex items-center justify-center gap-2 mt-3">
                                @if($profile->isOnline())
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                    </span>
                                    <span class="text-xs font-semibold text-green-600">Online</span>
                                @else
                                    <span class="inline-flex rounded-full h-2.5 w-2.5 bg-amber-400"></span>
                                    <span class="text-xs font-semibold text-amber-600">Away</span>
                                    <span class="text-xs text-slate-500">• {{ $profile->getLastSeenText() }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 px-4 py-4 text-center border-b border-slate-200">
                            <div class="flex flex-col items-center">
                                <div class="flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <div class="text-lg font-bold text-slate-900 leading-tight">{{ $viewCount ?? 0 }}</div>
                                </div>
                                <div class="text-[10px] uppercase tracking-wide text-slate-500 mt-1">Views</div>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <div class="text-lg font-bold text-slate-900 leading-tight">{{ $interestCount ?? 0 }}</div>
                                </div>
                                <div class="text-[10px] uppercase tracking-wide text-slate-500 mt-1">Interests</div>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    <div class="text-lg font-bold text-slate-900 leading-tight">{{ $favoriteCount ?? 0 }}</div>
                                </div>
                                <div class="text-[10px] uppercase tracking-wide text-slate-500 mt-1">Favorites</div>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            @if($isOwner)
                                <a href="{{ route('profile.complete-setup') }}"
                                    class="block w-full py-2.5 rounded-full font-bold text-white text-sm text-center bg-brand-700 hover:bg-brand-800 transition">
                                    Edit Profile
                                </a>
                            @else
                                @auth
                                    @if($isPaidMember)
                                        @if($canMessage)
                                            <form method="POST" action="{{ route('interests.send') }}">
                                                @csrf
                                                <input type="hidden" name="to_display_id" value="{{ $profile->display_id }}">
                                                <button class="w-full py-2.5 rounded-full font-bold text-white text-sm bg-brand-600 hover:bg-brand-700 transition">
                                                    Chat Now
                                                </button>
                                            </form>
                                        @else
                                            <button disabled class="w-full py-2.5 rounded-full font-bold text-white text-sm bg-gray-400 cursor-not-allowed">
                                                Chat Unavailable
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('packages.index') }}"
                                            class="block w-full py-2.5 rounded-full font-bold text-white text-sm text-center bg-brand-600 hover:bg-brand-700 transition">
                                            Upgrade to Chat
                                        </a>
                                    @endif

                                    @if(isset($isFavorited) && $isFavorited)
                                        <form method="POST" action="{{ route('favorites.destroy', $profile->display_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="w-full py-2.5 rounded-full font-bold text-amber-600 border border-amber-400 text-sm bg-white hover:bg-amber-50 transition flex items-center justify-center gap-2">
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                                </svg>
                                                Unfavorite Profile
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('favorites.store', $profile->display_id) }}">
                                            @csrf
                                            <button class="w-full py-2.5 rounded-full font-bold text-slate-800 text-sm bg-amber-400 hover:bg-amber-500 transition flex items-center justify-center gap-2">
                                                <svg class="h-4 w-4 text-slate-800" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                                </svg>
                                                Favorite Profile
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                        class="block w-full py-2.5 rounded-full font-bold text-white text-sm text-center bg-brand-600 hover:bg-brand-700 transition">
                                        Login to Chat
                                    </a>
                                    <a href="{{ route('login') }}"
                                        class="block w-full py-2.5 rounded-full font-bold text-amber-600 border border-amber-400 text-sm bg-white hover:bg-amber-50 transition text-center flex items-center justify-center gap-2">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                        </svg>
                                        Login to Favorite
                                    </a>
                                @endauth
                            @endif
                        </div>

                         <!-- Contact Details Section -->
                        <div class="px-4 pb-4 border-t border-slate-200">
                             <div class="profile-section-title text-slate-700 mb-3 mt-4">Contact Details</div>
                             @auth
                                 @if($isOwner || $isPaidMember)
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-slate-500">Mobile</span>
                                            <span class="text-sm font-bold text-slate-800">{{ $profile->mobile_no ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-slate-500">Email</span>
                                            <span class="text-sm font-bold text-slate-800">{{ $profile->user?->email ?? 'Hidden' }}</span>
                                        </div>
                                    </div>
                                 @else
                                    <div class="text-center py-4 bg-slate-50 rounded-lg border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <p class="text-xs text-slate-500 mb-3">Contact details are locked.</p>
                                        <a href="{{ route('packages.index') }}" class="inline-block px-4 py-1.5 rounded-full text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                                            Upgrade to View
                                        </a>
                                    </div>
                                 @endif
                             @else
                                <div class="text-center py-4 bg-slate-50 rounded-lg border border-slate-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    <p class="text-xs text-slate-500 mb-3">Login to view contact details</p>
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('login') }}" class="inline-block px-4 py-1.5 rounded-full text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                                            Login
                                        </a>
                                        <a href="{{ route('register') }}" class="inline-block px-4 py-1.5 rounded-full text-xs font-bold text-brand-600 bg-white border border-brand-600 hover:bg-brand-50 transition">
                                            Register
                                        </a>
                                    </div>
                                </div>
                             @endauth
                        </div>

                        <!-- Moderation -->
                        @if(!$isOwner && auth()->check())
                        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 flex gap-2 justify-between rounded-b-2xl">
                             <form method="POST" action="{{ route('reports.store', $profile->display_id) }}" class="w-1/2">
                                @csrf
                                <button onclick="return confirm('Are you sure you want to report this profile? Our moderation team will investigate.')" class="w-full text-xs font-bold text-slate-500 hover:text-red-600 transition flex items-center justify-center gap-1 py-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Report Profile
                                </button>
                             </form>
                             <div class="w-px bg-slate-200"></div>
                             <form method="POST" action="{{ route('blocks.store', $profile->display_id) }}" class="w-1/2">
                                @csrf
                                <button onclick="return confirm('Are you sure you want to block this user? You will no longer see their profile or messages.')" class="w-full text-xs font-bold text-slate-500 hover:text-red-600 transition flex items-center justify-center gap-1 py-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Block User
                                </button>
                             </form>
                        </div>
                        @else
                            <div class="h-4"></div>
                        @endif
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 text-center">
                        <div class="profile-section-title text-slate-700 mb-3">Profile QR Code</div>
                        <div class="qr-code mx-auto" data-qr-value="{{ $profileUrl }}" data-qr-download="#profile-qr-download"
                            role="img" aria-label="QR code linking to this profile"></div>
                        <a href="#" id="profile-qr-download" download="profile-qr-code.png"
                            class="mt-3 inline-block text-xs font-bold text-brand-700 hover:text-brand-800">Download QR Code</a>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5"
                        x-data="{
                            galleryQrOpen: false,
                            galleryViewerOpen: false,
                            galleryIndex: 0,
                            galleryItems: @js($gallerySlides),
                            openGallery(index) {
                                this.galleryIndex = index;
                                this.galleryViewerOpen = true;
                                document.body.classList.add('overflow-hidden');
                            },
                            closeGallery() {
                                this.galleryViewerOpen = false;
                                document.body.classList.remove('overflow-hidden');
                            },
                            nextGallery() {
                                if (!this.galleryItems.length) return;
                                this.galleryIndex = (this.galleryIndex + 1) % this.galleryItems.length;
                            },
                            prevGallery() {
                                if (!this.galleryItems.length) return;
                                this.galleryIndex = (this.galleryIndex - 1 + this.galleryItems.length) % this.galleryItems.length;
                            },
                            currentPhoto() {
                                return this.galleryItems[this.galleryIndex] || null;
                            }
                        }"
                        @keydown.escape.window="if (galleryViewerOpen) closeGallery(); if (galleryQrOpen) galleryQrOpen = false;"
                        @keydown.arrow-right.window="if (galleryViewerOpen) nextGallery()"
                        @keydown.arrow-left.window="if (galleryViewerOpen) prevGallery()">
                        <div class="flex items-center justify-between mb-3">
                            <div class="profile-section-title text-slate-700">Photo Gallery</div>
                            <span class="text-[11px] font-bold text-slate-500">
                                {{ $galleryPhotos->count() }}/{{ $galleryLimit }} Photos
                            </span>
                        </div>

                        @if($isOwner)
                            <div class="mb-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <button type="button" @click="$refs.galleryFileInput.click()"
                                    @disabled($galleryIsFull)
                                    class="w-full py-2 rounded-lg text-xs font-bold border border-slate-300 text-slate-700 hover:bg-slate-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Upload Here
                                </button>
                                @if($galleryUploadUrl)
                                    <button type="button" @click="galleryQrOpen = true"
                                        @disabled($galleryIsFull)
                                        class="w-full py-2 rounded-lg text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        Upload via QR Code
                                    </button>
                                @endif
                            </div>
                            @if($galleryIsFull)
                                <p class="mb-3 text-[11px] font-semibold text-amber-700">Gallery limit reached (4 photos). Delete one to upload another.</p>
                            @endif

                            <form action="{{ route('profile.update-section', 'gallery') }}" method="POST" enctype="multipart/form-data" class="hidden">
                                @csrf
                                <input type="file" name="photo" x-ref="galleryFileInput" @change="$el.form.submit()" accept="image/*">
                            </form>
                        @endif

                        @if($galleryPhotos->isEmpty())
                            <div class="text-center py-6 bg-slate-50 rounded-lg border border-slate-100">
                                <p class="text-xs text-slate-500">
                                    {{ $isOwner ? 'No photos yet. Add photos from your phone to build your gallery.' : 'No gallery photos available.' }}
                                </p>
                            </div>
                        @else
                            @php
                                $galleryGridClass = $galleryPhotos->count() === 1 ? 'grid-cols-1' : 'grid-cols-2';
                                $galleryCardAspect = $galleryPhotos->count() === 1 ? 'aspect-[4/5]' : 'aspect-[3/4]';
                            @endphp
                            <div class="grid {{ $galleryGridClass }} gap-3">
                                @foreach($galleryPhotos as $galleryPhoto)
                                    <div class="relative group overflow-hidden rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition">
                                        <button type="button" @click="openGallery({{ $loop->index }})" class="block w-full">
                                            <div class="{{ $galleryCardAspect }} bg-slate-100">
                                                <img src="{{ asset('storage/' . $galleryPhoto->path) }}"
                                                    alt="{{ $profile->first_name ?: $profile->display_id }} gallery photo"
                                                    class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-300"
                                                    loading="lazy" decoding="async">
                                            </div>
                                        </button>
                                        @if($isOwner)
                                            <div class="absolute top-2 right-2 z-10 flex flex-col gap-2" x-show="!galleryViewerOpen">
                                                <form method="POST" action="{{ route('photos.destroy', $galleryPhoto) }}"
                                                    onsubmit="return confirm('Delete this gallery photo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="h-8 w-8 rounded-full bg-red-600/90 backdrop-blur text-white hover:bg-red-700 transition shadow-md inline-flex items-center justify-center" title="Delete Gallery Photo">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('photos.primary', $galleryPhoto) }}"
                                                    onsubmit="return confirm('Make this your primary photo?')">
                                                    @csrf
                                                    <button type="submit" class="h-8 w-8 rounded-full bg-brand-600/90 backdrop-blur text-white hover:bg-brand-700 transition shadow-md inline-flex items-center justify-center" title="Set as Primary Profile Photo">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if(!$galleryPhotos->isEmpty())
                                <button type="button" @click="openGallery(0)"
                                    class="mt-3 w-full py-2 rounded-lg text-xs font-bold border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                                    Gallery / Photos View
                                </button>
                            @endif
                        @endif

                        <template x-teleport="body">
                            <div x-show="galleryViewerOpen" x-cloak x-transition.opacity
                                class="fixed inset-0 z-[99999] bg-black/90 backdrop-blur-sm"
                                @click.self="closeGallery()">
                                <div class="absolute z-[100001] flex items-center gap-2"
                                    style="top: calc(env(safe-area-inset-top, 0px) + 12px); right: 12px;">
                                    <div class="px-3 py-1 rounded-full bg-black/55 text-white text-xs font-bold">
                                        <span x-text="galleryIndex + 1"></span>
                                        <span>/</span>
                                        <span x-text="galleryItems.length"></span>
                                    </div>
                                    <button type="button" @click="closeGallery()"
                                        class="h-10 w-10 rounded-full border border-white/80 bg-black/65 text-white shadow-lg inline-flex items-center justify-center hover:bg-black/80 transition"
                                        aria-label="Close gallery">
                                        <span aria-hidden="true" class="text-xl leading-none">&times;</span>
                                    </button>
                                </div>

                                <button type="button" x-show="galleryItems.length > 1" @click.stop="prevGallery()"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 z-[100001] h-12 w-12 rounded-full border border-white/80 bg-slate-900/60 text-white text-3xl leading-none shadow-xl inline-flex items-center justify-center"
                                    aria-label="Previous photo">
                                    <span aria-hidden="true">&#8249;</span>
                                </button>

                                <button type="button" x-show="galleryItems.length > 1" @click.stop="nextGallery()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 z-[100001] h-12 w-12 rounded-full border border-white/80 bg-slate-900/60 text-white text-3xl leading-none shadow-xl inline-flex items-center justify-center"
                                    aria-label="Next photo">
                                    <span aria-hidden="true">&#8250;</span>
                                </button>

                                <div class="absolute inset-0 flex items-center justify-center px-4 pt-20 pb-28 md:px-10 md:pt-24 md:pb-32">
                                    <img :src="currentPhoto() ? currentPhoto().url : ''"
                                        :alt="currentPhoto() ? currentPhoto().alt : 'Gallery photo'"
                                        class="w-auto h-auto object-contain rounded-xl shadow-2xl"
                                        style="max-width:min(92vw,860px); max-height:calc(100vh - 220px);">
                                </div>

                                <div x-show="galleryItems.length > 1" class="absolute left-0 right-0 bottom-16 z-[100001] px-4">
                                    <div class="mx-auto w-fit max-w-full overflow-x-auto rounded-full bg-black/45 px-3 py-2 backdrop-blur">
                                        <div class="inline-flex items-center gap-2">
                                            <template x-for="(item, idx) in galleryItems" :key="idx">
                                                <button type="button" @click="galleryIndex = idx"
                                                    class="h-3 w-3 rounded-full border border-white/80 transition"
                                                    :class="galleryIndex === idx ? 'bg-white scale-110' : 'bg-transparent opacity-75 hover:opacity-100'"
                                                    :aria-label="`Go to photo ${idx + 1}`"></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="absolute left-0 right-0 z-[100001] flex items-center justify-center px-3"
                                    style="bottom: calc(env(safe-area-inset-bottom, 0px) + 10px);">
                                    <div class="inline-flex w-full max-w-[420px] flex-wrap items-center justify-center gap-2 rounded-2xl bg-black/55 px-2 py-2 backdrop-blur">
                                        <button type="button" x-show="galleryItems.length > 1" @click.stop="prevGallery()"
                                            class="min-w-[96px] rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-bold tracking-wide text-slate-800 transition hover:bg-slate-100">
                                            Previous
                                        </button>
                                        <button type="button" x-show="galleryItems.length > 1" @click.stop="nextGallery()"
                                            class="min-w-[96px] rounded-full border border-sky-200 bg-sky-600 px-3 py-2 text-xs font-bold tracking-wide !text-white transition hover:bg-sky-700">
                                            Next
                                        </button>
                                        <button type="button" @click="closeGallery()"
                                            class="min-w-[96px] rounded-full border border-slate-300 bg-slate-700 px-3 py-2 text-xs font-bold tracking-wide text-white transition hover:bg-slate-800">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        @if($isOwner && $galleryUploadUrl)
                            <p class="mt-3 text-[11px] text-slate-400">Scan QR from your phone to add photos quickly.</p>

                            <div x-show="galleryQrOpen" x-cloak class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                                <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-xl" @click.outside="galleryQrOpen = false">
                                    <div class="profile-section-title text-slate-700 mb-3">Upload Gallery Photos</div>
                                    <div class="qr-code mx-auto" data-qr-value="{{ $galleryUploadUrl }}"
                                        role="img" aria-label="QR code for uploading gallery photos from a phone"></div>
                                    <a href="{{ $galleryUploadUrl }}" target="_blank" rel="noopener noreferrer"
                                        class="mt-4 inline-block text-xs font-bold text-brand-700 hover:text-brand-800">
                                        Open Upload Link
                                    </a>
                                    <button type="button" @click="galleryQrOpen = false"
                                        class="mt-4 w-full py-2 rounded-lg text-xs font-bold border border-slate-300 text-slate-600 hover:bg-slate-50 transition">
                                        Close
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <div class="profile-section-title text-slate-700 mb-2">Profile Link</div>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $profileUrl }}" readonly
                                class="flex-1 text-xs text-slate-600 border border-slate-200 rounded-lg px-3 py-2 bg-slate-50">
                            <button type="button" data-copy-target="{{ $profileUrl }}"
                                class="px-3 py-2 text-xs font-bold rounded-lg text-white bg-brand-700 hover:bg-brand-800 transition">
                                Copy
                            </button>
                        </div>
                        <p class="mt-2 text-[11px] text-slate-400">Share your profile link easily.</p>
                    </div>

                    @auth
                        @if(!$isOwner)
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5"
                                x-data="{ reportOpen: {{ ($errors->has('category') || $errors->has('details') || $errors->has('report')) ? 'true' : 'false' }} }"
                                @keydown.escape.window="if (reportOpen) reportOpen = false">
                                <div class="profile-section-title text-slate-700 mb-3">Safety & Actions</div>
                                @if($errors->has('report'))
                                    <p class="mb-3 text-xs font-semibold text-red-600">{{ $errors->first('report') }}</p>
                                @endif
                                <div class="flex gap-2">
                                    <button type="button" @click="reportOpen = true"
                                        class="flex-1 py-2 rounded-lg border text-xs font-bold text-brand-700 border-brand-700 hover:bg-brand-50 transition">
                                        Report
                                    </button>
                                    <form method="POST" action="{{ route('blocks.store', $profile->display_id) }}" class="flex-1">
                                        @csrf
                                        <button class="w-full py-2 rounded-lg border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                            Block
                                        </button>
                                    </form>
                                </div>

                                <template x-teleport="body">
                                    <div x-show="reportOpen" x-cloak x-transition.opacity
                                        class="fixed inset-0 z-[99998] bg-black/60 backdrop-blur-sm p-4 flex items-center justify-center"
                                        @click.self="reportOpen = false">
                                        <div class="w-full max-w-lg rounded-2xl bg-white border border-slate-200 shadow-2xl p-5">
                                            <div class="flex items-center justify-between">
                                                <div class="profile-section-title text-slate-700">Report This Profile</div>
                                                <button type="button" @click="reportOpen = false"
                                                    class="h-9 w-9 rounded-full border border-slate-300 text-slate-500 hover:bg-slate-50 transition inline-flex items-center justify-center"
                                                    aria-label="Close report form">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <form method="POST" action="{{ route('reports.store', $profile->display_id) }}" class="space-y-3 mt-4">
                                                @csrf
                                                <div>
                                                    <label class="profile-label">Report Category</label>
                                                    <select name="category" class="mt-1 w-full rounded-lg border-slate-200 text-sm" required>
                                                        <option value="">Select</option>
                                                        <option value="Fake Profile" @selected(old('category') === 'Fake Profile')>Fake Profile</option>
                                                        <option value="Harassment / Abusive Behavior" @selected(old('category') === 'Harassment / Abusive Behavior')>Harassment / Abusive Behavior</option>
                                                        <option value="Inappropriate / Obscene Content" @selected(old('category') === 'Inappropriate / Obscene Content')>Inappropriate / Obscene Content</option>
                                                        <option value="Fraud / Asking for Money" @selected(old('category') === 'Fraud / Asking for Money')>Fraud / Asking for Money</option>
                                                        <option value="Spam or Mass Messaging" @selected(old('category') === 'Spam or Mass Messaging')>Spam or Mass Messaging</option>
                                                        <option value="Wrong Gender / Intentions" @selected(old('category') === 'Wrong Gender / Intentions')>Wrong Gender / Intentions</option>
                                                        <option value="Underage / Too Young" @selected(old('category') === 'Underage / Too Young')>Underage / Too Young</option>
                                                        <option value="Other" @selected(old('category') === 'Other')>Other</option>
                                                    </select>
                                                    @error('category')
                                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="profile-label">Reason</label>
                                                    <textarea name="details" rows="4" required minlength="10"
                                                        class="mt-1 w-full rounded-lg border-slate-200 text-sm"
                                                        placeholder="Please tell us why you are reporting this user...">{{ old('details') }}</textarea>
                                                    @error('details')
                                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" @click="reportOpen = false"
                                                        class="px-4 py-2 text-xs font-bold rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 transition">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-xs font-bold rounded-lg text-white bg-brand-700 hover:bg-brand-800 transition">
                                                        Submit Report
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @endif
                    @endauth
                </aside>

                <div class="lg:col-span-2 space-y-4">
                    <!-- About Me -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ isEditing: false }">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600 flex justify-between items-center">
                            <span class="profile-section-title">About Me</span>
                            @if($isOwner)
                                <button @click="isEditing = !isEditing" class="text-white hover:text-gray-200 focus:outline-none transition" title="Edit Section">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <div x-show="!isEditing" class="p-4 text-slate-600 text-sm leading-relaxed">
                            @if($profile->bio)
                                {{ $profile->bio }}
                            @elseif($isOwner)
                                <div class="text-slate-400 italic">
                                    Tell potential matches about yourself! Click the edit icon to add your bio.
                                </div>
                            @else
                                No bio available.
                            @endif
                        </div>

                        <div x-show="isEditing" x-cloak class="p-4 bg-slate-50">
                            <form action="{{ route('profile.update-section', 'bio') }}" method="POST">
                                @csrf
                                <textarea name="bio" rows="6" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500" placeholder="Tell us about yourself...">{{ $profile->bio }}</textarea>
                                <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-slate-200">
                                    <button type="button" @click="isEditing = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ isEditing: false }">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600 flex justify-between items-center">
                            <span class="profile-section-title">Personal Information</span>
                            @if($isOwner)
                                <button @click="isEditing = !isEditing" class="text-white hover:text-gray-200 focus:outline-none transition" title="Edit Section">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- View Mode -->
                        <div x-show="!isEditing" class="p-4 grid md:grid-cols-2 gap-4">
                            @foreach($personalRows as $row)
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="profile-label">{{ $row[0] }}</span>
                                    <span class="profile-value">{{ \App\Support\DisplayValue::text($row[1], 'N/A') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Edit Mode -->
                        <div x-show="isEditing" x-cloak class="p-4 bg-slate-50">
                            <form action="{{ route('profile.update-section', 'personal') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @csrf
                                <div>
                                    <label class="profile-label block mb-1">Gender</label>
                                    <select name="gender" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="male" {{ $profile->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $profile->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="{{ $profile->dob ? $profile->dob->format('Y-m-d') : '' }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Country</label>
                                    <input type="text" name="country" value="{{ $profile->country }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">State</label>
                                    <input type="text" name="state" value="{{ $profile->state }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">City</label>
                                    <input type="text" name="city" value="{{ $profile->city }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Marital Status</label>
                                    <select name="marital_status" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="Single" {{ $profile->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Divorced" {{ $profile->marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Widowed" {{ $profile->marital_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Married" {{ $profile->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Mother Tongue</label>
                                    <input type="text" name="mother_tongue" value="{{ $profile->mother_tongue }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Caste</label>
                                    <input type="text" name="caste" value="{{ $profile->caste }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Country of Origin</label>
                                    <input type="text" name="origin_country" value="{{ $profile->origin_country }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">State of Origin</label>
                                    <input type="text" name="origin_state" value="{{ $profile->origin_state }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">City of Origin</label>
                                    <input type="text" name="origin_city" value="{{ $profile->origin_city }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Willing to Relocate</label>
                                    <select name="willing_to_relocate" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="Yes" {{ $profile->willing_to_relocate == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $profile->willing_to_relocate == 'No' ? 'selected' : '' }}>No</option>
                                        <option value="Maybe" {{ $profile->willing_to_relocate == 'Maybe' ? 'selected' : '' }}>Maybe</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Looking to Marry</label>
                                    <select name="looking_to_marry" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="ASAP" {{ $profile->looking_to_marry == 'ASAP' ? 'selected' : '' }}>ASAP</option>
                                        <option value="1-2 Years" {{ $profile->looking_to_marry == '1-2 Years' ? 'selected' : '' }}>1-2 Years</option>
                                        <option value="3-4 Years" {{ $profile->looking_to_marry == '3-4 Years' ? 'selected' : '' }}>3-4 Years</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="profile-label block mb-1">Disability</label>
                                    <select name="disability" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Select...</option>
                                        @foreach(\App\Helpers\FormOptions::disabilityOptions() as $opt)
                                            <option value="{{ $opt }}" {{ ($profile->disability ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Height (cm)</label>
                                    <input type="number" name="height_cm" value="{{ $profile->height_cm }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Weight (kg)</label>
                                    <input type="number" name="weight_kg" value="{{ $profile->weight_kg }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div class="md:col-span-2 flex justify-end gap-2 mt-2 pt-4 border-t border-slate-200">
                                    <button type="button" @click="isEditing = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ isEditing: false }">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600 flex justify-between items-center">
                            <span class="profile-section-title">Religion</span>
                            @if($isOwner)
                                <button @click="isEditing = !isEditing" class="text-white hover:text-gray-200 focus:outline-none transition" title="Edit Section">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- View Mode -->
                        <div x-show="!isEditing" class="p-4 grid md:grid-cols-2 gap-4">
                            @foreach($religionRows as $row)
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="profile-label">{{ $row[0] }}</span>
                                    <span class="profile-value">{{ \App\Support\DisplayValue::text($row[1], 'N/A') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Edit Mode -->
                        <div x-show="isEditing" x-cloak class="p-4 bg-slate-50">
                            <form action="{{ route('profile.update-section', 'religion') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @csrf
                                <div>
                                    <label class="profile-label block mb-1">Religion</label>
                                    <select name="religion" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="Islam" {{ $profile->religion == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Christianity" {{ $profile->religion == 'Christianity' ? 'selected' : '' }}>Christianity</option>
                                        <option value="Hinduism" {{ $profile->religion == 'Hinduism' ? 'selected' : '' }}>Hinduism</option>
                                        <option value="Other" {{ $profile->religion == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Sect</label>
                                    <select name="sect" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="Sunni" {{ $profile->sect == 'Sunni' ? 'selected' : '' }}>Sunni</option>
                                        <option value="Shia" {{ $profile->sect == 'Shia' ? 'selected' : '' }}>Shia</option>
                                        <option value="Other" {{ $profile->sect == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Are You Revert?</label>
                                    <select name="is_revert" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="1" {{ $profile->is_revert == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ $profile->is_revert == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 flex justify-end gap-2 mt-2 pt-4 border-t border-slate-200">
                                    <button type="button" @click="isEditing = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Family Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden"
                        x-data="{
                            isEditing: false,
                            marriedBrothers: {{ $marriedBrothersCount }},
                            marriedSisters: {{ $marriedSistersCount }},
                            marriedTotal() {
                                return (parseInt(this.marriedBrothers) || 0) + (parseInt(this.marriedSisters) || 0);
                            }
                        }">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600 flex justify-between items-center">
                            <span class="profile-section-title">Family Details</span>
                            @if($isOwner)
                                <button @click="isEditing = !isEditing" class="text-white hover:text-gray-200 focus:outline-none transition" title="Edit Section">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- View Mode -->
                        <div x-show="!isEditing" class="p-4 grid md:grid-cols-2 gap-4">
                            @foreach($familyRows as $row)
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="profile-label">{{ $row[0] }}</span>
                                    <span class="profile-value">{{ \App\Support\DisplayValue::text($row[1], 'N/A') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Edit Mode -->
                        <div x-show="isEditing" x-cloak class="p-4 bg-slate-50">
                            <form action="{{ route('profile.update-section', 'family') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @csrf
                                <div>
                                    <label class="profile-label block mb-1">Father Name</label>
                                    <input type="text" name="father_name" value="{{ $profile->father_name }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Father Occupation</label>
                                    <input type="text" name="father_occupation" value="{{ $profile->father_occupation }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Mother Occupation</label>
                                    <input type="text" name="mother_occupation" value="{{ $profile->mother_occupation }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Brothers</label>
                                    <input type="number" name="brothers_count" value="{{ $brothersCount }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Married Brothers</label>
                                    <input type="number" name="married_brothers_count" x-model.number="marriedBrothers" min="0" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Sisters</label>
                                    <input type="number" name="sisters_count" value="{{ $sistersCount }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Married Sisters</label>
                                    <input type="number" name="married_sisters_count" x-model.number="marriedSisters" min="0" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <input type="hidden" name="married_siblings_count" :value="marriedTotal()">
                                <div class="md:col-span-2 -mt-1">
                                    <p class="text-xs text-slate-500">Married siblings total: <span class="font-semibold text-slate-700" x-text="marriedTotal()"></span></p>
                                </div>
                                <div class="md:col-span-2 flex justify-end gap-2 mt-2 pt-4 border-t border-slate-200">
                                    <button type="button" @click="isEditing = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ isEditing: false }">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600 flex justify-between items-center">
                            <span class="profile-section-title">Career Information</span>
                            @if($isOwner)
                                <button @click="isEditing = !isEditing" class="text-white hover:text-gray-200 focus:outline-none transition" title="Edit Section">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- View Mode -->
                        <div x-show="!isEditing" class="p-4 grid md:grid-cols-2 gap-4">
                            @foreach($careerRows as $row)
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="profile-label">{{ $row[0] }}</span>
                                    <span class="profile-value">{{ \App\Support\DisplayValue::text($row[1], 'N/A') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Edit Mode -->
                        <div x-show="isEditing" x-cloak class="p-4 bg-slate-50">
                            <form action="{{ route('profile.update-section', 'career') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @csrf
                                <div>
                                    <label class="profile-label block mb-1">Qualification</label>
                                    <input type="text" name="education" value="{{ $profile->education }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">University</label>
                                    <input type="text" name="university" value="{{ $profile->university }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Occupation</label>
                                    <input type="text" name="profession" value="{{ $profile->profession }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Job Post</label>
                                    <input type="text" name="nature_of_job" value="{{ $profile->nature_of_job }}" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Monthly Income</label>
                                    <select name="income" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Select Income...</option>
                                        @foreach(\App\Helpers\FormOptions::incomeOptions() as $opt)
                                            <option value="{{ $opt }}" {{ $profile->income == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2 flex justify-end gap-2 mt-2 pt-4 border-t border-slate-200">
                                    <button type="button" @click="isEditing = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>


                    <!-- Hobbies & Interest -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ isEditing: false }">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600 flex justify-between items-center">
                            <span class="profile-section-title">Hobbies & Interest</span>
                            @if($isOwner)
                                <button @click="isEditing = !isEditing" class="text-white hover:text-gray-200 focus:outline-none transition" title="Edit Section">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- View Mode -->
                        <div x-show="!isEditing" class="p-4">
                            @php
                                $hobbiesList = [];
                                if ($profile->hobbies) {
                                    $decoded = json_decode($profile->hobbies, true);
                                    if (is_array($decoded)) {
                                        $hobbiesList = $decoded;
                                    } else {
                                        $hobbiesList = array_map('trim', explode(',', $profile->hobbies));
                                    }
                                }
                            @endphp
                            @if(count($hobbiesList) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($hobbiesList as $hobby)
                                        <span class="px-3 py-1.5 text-sm font-semibold rounded-lg border border-slate-200 bg-slate-50 text-slate-700">{{ $hobby }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-400 italic">No hobbies added yet.</p>
                            @endif
                        </div>

                        <!-- Edit Mode -->
                        <div x-show="isEditing" x-cloak class="p-4 bg-slate-50">
                            <form action="{{ route('profile.update-section', 'hobbies') }}" method="POST">
                                @csrf
                                <div>
                                    <label class="profile-label block mb-1">Hobbies (comma-separated)</label>
                                    <input type="text" name="hobbies" value="{{ is_array($hobbiesList) ? implode(', ', $hobbiesList) : $profile->hobbies }}" placeholder="e.g. Photography, Cooking, Gaming" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                    <p class="text-xs text-slate-400 mt-1">Separate hobbies with commas</p>
                                </div>
                                <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-slate-200">
                                    <button type="button" @click="isEditing = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ isEditing: false }">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600 flex justify-between items-center">
                            <span class="profile-section-title">Property Details</span>
                            @if($isOwner)
                                <button @click="isEditing = !isEditing" class="text-white hover:text-gray-200 focus:outline-none transition" title="Edit Section">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- View Mode -->
                        <div x-show="!isEditing" class="p-4 grid md:grid-cols-2 gap-4">
                            @foreach($propertyRows as $row)
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="profile-label">{{ $row[0] }}</span>
                                    <span class="profile-value">{{ \App\Support\DisplayValue::text($row[1], 'N/A') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Edit Mode -->
                        <div x-show="isEditing" x-cloak class="p-4 bg-slate-50">
                            <form action="{{ route('profile.update-section', 'property') }}" method="POST" class="grid grid-cols-1 gap-4">
                                @csrf
                                <div>
                                    <label class="profile-label block mb-1">Home Status</label>
                                    <select name="home_status" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                        <option value="Own" {{ ($profile->home_status ?? '') == 'Own' ? 'selected' : '' }}>Own</option>
                                        <option value="Rent" {{ ($profile->home_status ?? '') == 'Rent' ? 'selected' : '' }}>Rent</option>
                                        <option value="Sharing" {{ ($profile->home_status ?? '') == 'Sharing' ? 'selected' : '' }}>Sharing</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Home Size</label>
                                    <input type="text" name="home_size" value="{{ $profile->home_size }}" placeholder="e.g. 10 Marla" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="profile-label block mb-1">Other Properties</label>
                                    <input type="text" name="other_properties" value="{{ $profile->other_properties }}" placeholder="e.g. Plot in DHA" class="w-full text-sm border-gray-300 rounded-md focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-slate-200">
                                    <button type="button" @click="isEditing = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Partner Preferences -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-4 py-3 text-white bg-gradient-to-r from-brand-700 to-brand-600">
                            <span class="profile-section-title">Partner Preferences</span>
                        </div>
                        
                        <div class="p-4 grid md:grid-cols-2 gap-4">
                            @foreach($preferenceRows as $row)
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="profile-label">{{ $row[0] }}</span>
                                    <span class="profile-value">{{ \App\Support\DisplayValue::text($row[1], 'N/A') }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if($isOwner)
                            <div class="bg-slate-50 p-3 text-center border-t border-slate-100">
                                <a href="{{ route('profile.preferences') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 transition">
                                    Edit Preferences
                                </a>
                            </div>
                        @endif
                    </div>

                    @if(!$isOwner)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
                            <div style="border: 1px solid #fecdd3; background: #fff1f2; border-radius: 12px; padding: 14px; text-align: center;">
                                <p style="font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #0f172a;">
                                    If you are interested in this profile, let's chat
                                </p>
                                <p style="margin-top: 4px; font-size: 12px; color: #334155;">اگر آپ اس پروفائل میں دلچسپی رکھتے ہیں تو ہم سے چیٹ کریں۔</p>
                                <a href="{{ $profileWhatsappUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="chat-cta-btn"
                                    style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; margin-top: 10px; padding: 9px 18px; border-radius: 999px; background: #16a34a; color: #ffffff; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; text-decoration: none;">
                                    <span>Let's Chat</span>
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- More Profiles -->
            @unless($isOwner)
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Interested in this profile?</h2>
                    <a href="{{ route('profiles.index') }}" class="text-sm font-bold text-brand-700 hover:text-brand-800">View All Matches</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
                    @forelse($relatedProfiles as $relatedProfile)
                        @php
                            $relatedPhotoUrl = $relatedProfile->primaryPhoto
                                ? \App\Support\Media::thumb($relatedProfile->primaryPhoto->path, 240)
                                : asset('images/avatar-placeholder.svg');
                        @endphp
                        <article class="related-match-card">
                            <div class="related-match-row">
                                <div class="related-match-avatar">
                                    <img src="{{ $relatedPhotoUrl }}"
                                        alt="{{ trim(($relatedProfile->first_name ?? '') . ' ' . ($relatedProfile->last_name ?? '')) ?: $relatedProfile->display_id }}"
                                        loading="lazy"
                                        decoding="async">
                                </div>
                                <div class="related-match-content">
                                    <h3 class="related-match-name">
                                        {{ trim(($relatedProfile->first_name ?? '') . ' ' . ($relatedProfile->last_name ?? '')) ?: $relatedProfile->display_id }}
                                    </h3>
                                    <div class="related-match-meta">
                                        <p class="related-match-meta-row">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $relatedProfile->age ? $relatedProfile->age . ' Years' : 'Age N/A' }}
                                        </p>
                                        <p class="related-match-meta-row truncate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V8a2 2 0 00-2-2h-3V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v1H6a2 2 0 00-2 2v5m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4" />
                                            </svg>
                                            {{ $relatedProfile->profession ?? 'Profession N/A' }}
                                        </p>
                                        <p class="related-match-meta-row truncate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $relatedProfile->country ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <a href="{{ route('profiles.show', $relatedProfile->display_id) }}"
                                        class="related-match-btn">
                                        Profile Detail
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="md:col-span-2 lg:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-200 p-4 text-center">
                            <div class="text-slate-400 text-sm">No similar profiles available right now.</div>
                        </div>
                    @endforelse
                </div>
            </div>
            @endunless
        </div>
    </div>

    <!-- Modals -->
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg text-sm font-bold z-50">
            {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
            class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg text-sm font-bold z-50">
            {{ session('error') }}
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('[data-copy-target]');
            buttons.forEach((button) => {
                button.addEventListener('click', async () => {
                    const value = button.getAttribute('data-copy-target');
                    try {
                        await navigator.clipboard.writeText(value);
                        button.textContent = 'Copied';
                        setTimeout(() => (button.textContent = 'Copy'), 1500);
                    } catch (err) {
                        button.textContent = 'Error';
                        setTimeout(() => (button.textContent = 'Copy'), 1500);
                    }
                });
            });
        });
    </script>

<script>
(function () {
    function enforceSiblingLimit(totalInput, marriedInput) {
        if (!totalInput || !marriedInput) return;
        var errorEl = marriedInput.parentNode.querySelector('.sibling-error');
        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.className = 'sibling-error text-xs text-red-600 mt-1 hidden';
            marriedInput.parentNode.appendChild(errorEl);
        }
        function validate() {
            var total = parseInt(totalInput.value) || 0;
            var married = parseInt(marriedInput.value) || 0;
            marriedInput.max = total;
            if (married > total) {
                marriedInput.value = total;
                errorEl.textContent = 'Married count cannot exceed total (' + total + ').';
                errorEl.classList.remove('hidden');
            } else {
                errorEl.classList.add('hidden');
            }
        }
        totalInput.addEventListener('input', validate);
        marriedInput.addEventListener('input', validate);
        validate();
    }
    document.addEventListener('DOMContentLoaded', function () {
        enforceSiblingLimit(
            document.querySelector('[name="brothers_count"]'),
            document.querySelector('[name="married_brothers_count"]')
        );
        enforceSiblingLimit(
            document.querySelector('[name="sisters_count"]'),
            document.querySelector('[name="married_sisters_count"]')
        );
    });
})();
</script>

<script src="{{ asset('static/qrcode.min.js') }}"></script>
<script>
(function () {
    function renderQrCodes() {
        document.querySelectorAll('.qr-code').forEach(function (el) {
            if (el.dataset.qrRendered || !window.QRCode) {
                return;
            }
            el.dataset.qrRendered = '1';
            new QRCode(el, {
                text: el.dataset.qrValue,
                width: 220,
                height: 220,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
            var link = el.dataset.qrDownload ? document.querySelector(el.dataset.qrDownload) : null;
            if (link) {
                // qrcodejs fills the <img> just after construction, so read the canvas on click.
                link.addEventListener('click', function () {
                    var canvas = el.querySelector('canvas');
                    if (canvas) {
                        link.href = canvas.toDataURL('image/png');
                    }
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderQrCodes);
    } else {
        renderQrCodes();
    }
})();
</script>

@endsection
{{-- Verified Complete Rewrite --}}

