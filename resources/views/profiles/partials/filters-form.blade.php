@once
    <style>
        .filter-panel { overflow: hidden; border: 1px solid #e7dfe2; border-radius: 14px; background: #fff; box-shadow: 0 5px 22px rgba(62, 35, 45, .05); font-family: "Manrope", sans-serif; }
        .filter-heading { display: flex; align-items: center; gap: 11px; padding: 18px; border-bottom: 1px solid #eee7ea; background: linear-gradient(135deg, #fff 0%, #fdf8f9 100%); }
        .filter-heading-icon { display: inline-flex; align-items: center; justify-content: center; width: 37px; height: 37px; flex-shrink: 0; border-radius: 10px; background: #f6e8ed; color: #9d2b48; }
        .filter-heading-icon svg { width: 19px; height: 19px; }
        .filter-heading h2 { margin: 0; color: #3d3036; font-size: 15px; font-weight: 800; }
        .filter-heading p { margin: 3px 0 0; color: #91838a; font-size: 10px; }
        .filter-heading-count { display: inline-flex; align-items: center; justify-content: center; min-width: 23px; height: 23px; margin-left: auto; padding: 0 6px; border-radius: 999px; background: #9d2b48; color: #fff; font-size: 10px; font-weight: 800; }
        .filter-saved { padding: 12px 16px; border-bottom: 1px solid #eee7ea; background: #fffaf1; }
        .filter-saved-toggle { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0; border: 0; background: transparent; color: #865d21; font-size: 11px; font-weight: 800; cursor: pointer; }
        .filter-saved-toggle span { display: flex; align-items: center; gap: 6px; }
        .filter-saved-toggle svg { width: 15px; height: 15px; }
        .filter-saved-list { display: grid; gap: 7px; margin-top: 11px; }
        .filter-saved-item { display: flex; align-items: center; gap: 6px; padding: 8px 9px; border: 1px solid #eddfc6; border-radius: 8px; background: #fff; }
        .filter-saved-item a { min-width: 0; flex: 1; overflow: hidden; color: #765526; font-size: 11px; font-weight: 700; text-decoration: none; text-overflow: ellipsis; white-space: nowrap; }
        .filter-saved-item button { padding: 2px; border: 0; background: transparent; color: #b0a28f; cursor: pointer; }
        .filter-saved-item button:hover { color: #b42345; }
        .filter-saved-item button svg { display: block; width: 14px; height: 14px; }
        .filter-section { border-bottom: 1px solid #eee7ea; }
        .filter-section-toggle { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 15px 17px; border: 0; background: #fff; color: #4b3d44; font-size: 13px; font-weight: 800; text-align: left; cursor: pointer; }
        .filter-section-toggle:hover { background: #fdfafb; color: #922844; }
        .filter-section-toggle svg { width: 15px; height: 15px; color: #a7989f; transition: transform .18s ease; }
        .filter-section-body { display: grid; gap: 13px; padding: 0 17px 17px; }
        .filter-field { min-width: 0; }
        .filter-field > label, .filter-label { display: block; margin-bottom: 6px; color: #81737a; font-size: 11px; font-weight: 800; letter-spacing: .02em; }
        .filter-input, .filter-select { width: 100%; height: 39px; padding: 0 11px; border: 1px solid #ded6da; border-radius: 8px; outline: none; background: #fff; color: #4d4046; font-family: "Manrope", sans-serif; font-size: 12px; }
        .filter-input::placeholder { color: #aa9fa4; }
        .filter-input:focus, .filter-select:focus { border-color: #b55770; box-shadow: 0 0 0 3px rgba(157, 43, 72, .08); }
        .filter-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
        .filter-radios { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
        .filter-radio { position: relative; cursor: pointer; }
        .filter-radio input { position: absolute; width: 1px; height: 1px; opacity: 0; }
        .filter-radio span { display: flex; align-items: center; justify-content: center; height: 36px; border: 1px solid #ded6da; border-radius: 8px; background: #fff; color: #75666d; font-size: 10px; font-weight: 800; transition: all .16s ease; }
        .filter-radio input:checked + span { border-color: #9d2b48; background: #9d2b48; color: #fff; box-shadow: 0 3px 9px rgba(157, 43, 72, .16); }
        .filter-radio input:focus-visible + span { outline: 2px solid #9d2b48; outline-offset: 2px; }
        .filter-check { display: flex; align-items: flex-start; gap: 8px; color: #5d4e55; font-size: 12px; line-height: 1.5; cursor: pointer; }
        .filter-check input { width: 15px; height: 15px; flex-shrink: 0; margin: 0; accent-color: #9d2b48; }
        .filter-check small { display: block; margin-top: 2px; color: #9b8e94; font-size: 9px; }
        .filter-actions { display: grid; grid-template-columns: 1fr auto; gap: 9px; padding: 16px 17px 10px; }
        .filter-submit { height: 42px; padding: 0 16px; border: 0; border-radius: 9px; background: #9d2b48; color: #fff; font-family: "Manrope", sans-serif; font-size: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 5px 12px rgba(157, 43, 72, .17); }
        .filter-submit:hover { background: #862139; }
        .filter-reset { display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 14px; border: 1px solid #ddd3d8; border-radius: 9px; color: #74636b; font-size: 11px; font-weight: 800; text-decoration: none; }
        .filter-reset:hover { border-color: #c7afb9; color: #9d2b48; }
        .filter-save { display: flex; align-items: center; justify-content: center; gap: 7px; width: calc(100% - 34px); height: 37px; margin: 0 17px 17px; border: 0; border-radius: 8px; background: #f8f1e4; color: #876126; font-family: "Manrope", sans-serif; font-size: 10px; font-weight: 800; cursor: pointer; }
        .filter-save svg { width: 14px; height: 14px; }
        .filter-save:hover { background: #f3e7d2; }
        .filter-modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(31, 20, 25, .58); }
        .filter-modal-card { position: relative; width: min(400px, 100%); padding: 25px; border-radius: 15px; background: #fff; box-shadow: 0 24px 70px rgba(30, 16, 22, .25); }
        .filter-modal-card h3 { margin: 0 0 7px; color: #3d3036; font-family: "Playfair Display", serif; font-size: 24px; }
        .filter-modal-card > div > p { margin: 0 0 19px; color: #81747a; font-size: 12px; line-height: 1.6; }
        .filter-modal-close { position: absolute; top: 15px; right: 15px; padding: 4px; border: 0; background: transparent; color: #94868d; cursor: pointer; }
        .filter-modal-close svg { width: 19px; height: 19px; }
        .filter-modal-submit { width: 100%; height: 42px; margin-top: 14px; border: 0; border-radius: 9px; background: #9d2b48; color: #fff; font-size: 12px; font-weight: 800; cursor: pointer; }
        @media (max-width: 1023px) {
            .filter-section-body { padding-right: 19px; padding-left: 19px; }
            .filter-input, .filter-select { height: 42px; font-size: 12px; }
            .filter-radio span { height: 40px; font-size: 11px; }
        }
    </style>
@endonce

<div class="filter-panel" x-data="{ saveModalOpen: false }">
    <div class="filter-heading">
        <span class="filter-heading-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M7 12h10M10 18h4"/></svg>
        </span>
        <div>
            <h2>Refine search</h2>
            <p>Choose your partner preferences</p>
        </div>
        @if(($activeFilterCount ?? 0) > 0)
            <span class="filter-heading-count">{{ $activeFilterCount }}</span>
        @endif
    </div>

    @auth
        @if(isset($savedSearches) && $savedSearches->isNotEmpty())
            <div class="filter-saved" x-data="{ expanded: false }">
                <button type="button" class="filter-saved-toggle" @click="expanded = !expanded" :aria-expanded="expanded.toString()">
                    <span>
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12a1 1 0 011 1v16l-7-4-7 4V5a1 1 0 011-1z"/></svg>
                        My saved searches
                    </span>
                    <svg :style="expanded ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="expanded" x-cloak class="filter-saved-list">
                    @foreach($savedSearches as $savedSearch)
                        <div class="filter-saved-item">
                            <a href="{{ route('profiles.index', $savedSearch->filters_json) }}">{{ $savedSearch->name }}</a>
                            <form method="POST" action="{{ route('saved-searches.destroy', $savedSearch) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove saved search?')" aria-label="Delete {{ $savedSearch->name }}">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endauth

    <form id="search-filters-form" method="GET" action="{{ route('profiles.index') }}" onsubmit="this.querySelectorAll('input, select').forEach(function (field) { if (!field.value) field.disabled = true; });">
        <section x-data="{ open: true }" class="filter-section">
            <button type="button" class="filter-section-toggle" @click="open = !open" :aria-expanded="open.toString()">
                <span>Basic preferences</span>
                <svg :style="open ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="filter-section-body">
                <div class="filter-field">
                    <label for="filter-keyword">Keyword search</label>
                    <input class="filter-input" type="text" id="filter-keyword" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="City, education or profession" maxlength="100">
                </div>
                <div class="filter-field">
                    <span class="filter-label">Looking for</span>
                    <div class="filter-radios">
                        <label class="filter-radio">
                            <input type="radio" name="gender" value="all" @checked(empty($filters['gender']) || ($filters['gender'] ?? '') === 'all')>
                            <span>All</span>
                        </label>
                        <label class="filter-radio">
                            <input type="radio" name="gender" value="male" @checked(($filters['gender'] ?? '') === 'male')>
                            <span>Male</span>
                        </label>
                        <label class="filter-radio">
                            <input type="radio" name="gender" value="female" @checked(($filters['gender'] ?? '') === 'female')>
                            <span>Female</span>
                        </label>
                    </div>
                </div>
                <div class="filter-grid">
                    <div class="filter-field">
                        <label for="filter-age-min">Age from</label>
                        <select class="filter-select" id="filter-age-min" name="age_min">
                            <option value="">Any</option>
                            @for($age = 18; $age <= 70; $age++)
                                <option value="{{ $age }}" @selected(($filters['age_min'] ?? '') == $age)>{{ $age }} years</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="filter-age-max">Age to</label>
                        <select class="filter-select" id="filter-age-max" name="age_max">
                            <option value="">Any</option>
                            @for($age = 18; $age <= 70; $age++)
                                <option value="{{ $age }}" @selected(($filters['age_max'] ?? '') == $age)>{{ $age }} years</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <label class="filter-check">
                    <input type="checkbox" name="verified_only" value="1" @checked($filters['verified_only'] ?? false)>
                    <span>CNIC-verified profiles only<small>Show profiles with completed identity verification</small></span>
                </label>
            </div>
        </section>

        <section x-data="{ open: true }" class="filter-section">
            <button type="button" class="filter-section-toggle" @click="open = !open" :aria-expanded="open.toString()">
                <span>Location</span>
                <svg :style="open ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="filter-section-body">
                <div class="filter-field">
                    <label for="filter-country">Country</label>
                    <select class="filter-select" id="filter-country" name="country">
                        <option value="">Any country</option>
                        @foreach(['Pakistan', 'India', 'UAE', 'United Arab Emirates', 'UK', 'United Kingdom', 'USA', 'United States', 'Canada', 'Saudi Arabia', 'Australia', 'Other'] as $country)
                            <option value="{{ $country }}" @selected(($filters['country'] ?? '') === $country)>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-state">State / Province</label>
                    <select class="filter-select" id="filter-state" name="state">
                        <option value="">Any state</option>
                        @foreach(['Punjab', 'Sindh', 'Khyber Pakhtunkhwa', 'Balochistan', 'Islamabad Capital Territory', 'Gilgit-Baltistan', 'Azad Kashmir', 'Other'] as $state)
                            <option value="{{ $state }}" @selected(($filters['state'] ?? '') === $state)>{{ $state }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-city">City</label>
                    <select class="filter-select" id="filter-city" name="city">
                        <option value="">Any city</option>
                        @foreach(\App\Helpers\FormOptions::cities() as $city)
                            <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section x-data="{ open: false }" class="filter-section">
            <button type="button" class="filter-section-toggle" @click="open = !open" :aria-expanded="open.toString()">
                <span>Personal details</span>
                <svg :style="open ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="filter-section-body">
                <div class="filter-field">
                    <label for="filter-marital">Marital status</label>
                    <select class="filter-select" id="filter-marital" name="marital_status">
                        <option value="">Any status</option>
                        @foreach(\App\Helpers\FormOptions::maritalStatuses() as $status)
                            <option value="{{ $status }}" @selected(($filters['marital_status'] ?? '') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-language">Mother tongue</label>
                    <select class="filter-select" id="filter-language" name="mother_tongue">
                        <option value="">Any language</option>
                        @foreach(\App\Helpers\FormOptions::motherTongues() as $language)
                            <option value="{{ $language }}" @selected(($filters['mother_tongue'] ?? '') === $language)>{{ $language }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-grid">
                    <div class="filter-field">
                        <label for="filter-height-min">Min. height (cm)</label>
                        <input class="filter-input" type="number" id="filter-height-min" name="height_min" value="{{ $filters['height_min'] ?? '' }}" min="120" max="220" placeholder="120">
                    </div>
                    <div class="filter-field">
                        <label for="filter-height-max">Max. height (cm)</label>
                        <input class="filter-input" type="number" id="filter-height-max" name="height_max" value="{{ $filters['height_max'] ?? '' }}" min="120" max="220" placeholder="220">
                    </div>
                </div>
            </div>
        </section>

        <section x-data="{ open: false }" class="filter-section">
            <button type="button" class="filter-section-toggle" @click="open = !open" :aria-expanded="open.toString()">
                <span>Religion & community</span>
                <svg :style="open ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="filter-section-body">
                <div class="filter-field">
                    <label for="filter-religion">Religion</label>
                    <select class="filter-select" id="filter-religion" name="religion">
                        <option value="">Any religion</option>
                        @foreach(\App\Helpers\FormOptions::religions() as $religion)
                            <option value="{{ $religion }}" @selected(($filters['religion'] ?? '') === $religion)>{{ $religion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-caste">Caste</label>
                    <select class="filter-select" id="filter-caste" name="caste">
                        <option value="">Any caste</option>
                        @foreach(\App\Helpers\FormOptions::castes() as $caste)
                            <option value="{{ $caste }}" @selected(($filters['caste'] ?? '') === $caste)>{{ $caste }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-sect">Sect</label>
                    <select class="filter-select" id="filter-sect" name="sect">
                        <option value="">Any sect</option>
                        @foreach(\App\Helpers\FormOptions::sects() as $sect)
                            <option value="{{ $sect }}" @selected(($filters['sect'] ?? '') === $sect)>{{ $sect }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section x-data="{ open: false }" class="filter-section">
            <button type="button" class="filter-section-toggle" @click="open = !open" :aria-expanded="open.toString()">
                <span>Education & career</span>
                <svg :style="open ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="filter-section-body">
                <div class="filter-field">
                    <label for="filter-education">Education</label>
                    <select class="filter-select" id="filter-education" name="education">
                        <option value="">Any education</option>
                        @foreach(\App\Helpers\FormOptions::educationLevels() as $education)
                            <option value="{{ $education }}" @selected(($filters['education'] ?? '') === $education)>{{ $education }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-profession">Profession</label>
                    <select class="filter-select" id="filter-profession" name="profession">
                        <option value="">Any profession</option>
                        @foreach(\App\Helpers\FormOptions::professions() as $group => $professions)
                            <optgroup label="{{ $group }}">
                                @foreach($professions as $profession)
                                    <option value="{{ $profession }}" @selected(($filters['profession'] ?? '') === $profession)>{{ $profession }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section x-data="{ open: false }" class="filter-section">
            <button type="button" class="filter-section-toggle" @click="open = !open" :aria-expanded="open.toString()">
                <span>Membership type</span>
                <svg :style="open ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="filter-section-body">
                @foreach(\App\Models\Plan::where('is_active', true)->get() as $plan)
                    <label class="filter-check">
                        <input type="checkbox" name="plan[]" value="{{ strtolower($plan->name) }}" @checked(in_array(strtolower($plan->name), $filters['plan'] ?? []))>
                        <span>{{ $plan->name }}</span>
                    </label>
                @endforeach
            </div>
        </section>

        <section x-data="{ open: false }" class="filter-section">
            <button type="button" class="filter-section-toggle" @click="open = !open" :aria-expanded="open.toString()">
                <span>Search by profile ID</span>
                <svg :style="open ? 'transform: rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="filter-section-body">
                <div class="filter-field">
                    <label for="filter-profile-id">Profile ID</label>
                    <input class="filter-input" type="text" id="filter-profile-id" name="display_id" value="{{ $filters['display_id'] ?? '' }}" placeholder="e.g. AB1234" maxlength="50">
                </div>
            </div>
        </section>

        <div class="filter-actions">
            <button type="submit" class="filter-submit">Show matches</button>
            <a href="{{ route('profiles.index') }}" class="filter-reset">Reset</a>
        </div>
        @auth
            <button type="button" class="filter-save" @click="saveModalOpen = true">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12a1 1 0 011 1v16l-7-4-7 4V5a1 1 0 011-1z"/></svg>
                Save this search
            </button>
        @endauth
    </form>

    @auth
        <div x-show="saveModalOpen" x-cloak class="filter-modal" role="dialog" aria-modal="true" aria-labelledby="save-search-title">
            <div class="filter-modal-card" @click.away="saveModalOpen = false">
                <button type="button" class="filter-modal-close" @click="saveModalOpen = false" aria-label="Close">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div x-data="{
                    submitSave() {
                        const nameInput = document.getElementById('save-search-name');
                        if (!nameInput.reportValidity()) return;
                        const filterData = {};
                        const formData = new FormData(document.getElementById('search-filters-form'));
                        for (const [key, value] of formData.entries()) {
                            if (!value) continue;
                            if (key.endsWith('[]')) {
                                const arrayKey = key.replace('[]', '');
                                filterData[arrayKey] = filterData[arrayKey] || [];
                                filterData[arrayKey].push(value);
                            } else {
                                filterData[key] = value;
                            }
                        }
                        document.getElementById('save-search-name-input').value = nameInput.value;
                        document.getElementById('filters-json-input').value = JSON.stringify(filterData);
                        document.getElementById('save-search-form').submit();
                    }
                }">
                    <h3 id="save-search-title">Save this search</h3>
                    <p>Give these preferences a name so you can return to the same matches later.</p>
                    <div class="filter-field">
                        <label for="save-search-name">Search name</label>
                        <input class="filter-input" type="text" id="save-search-name" placeholder="e.g. Lahore doctors" required maxlength="100">
                    </div>
                    <button type="button" class="filter-modal-submit" @click="submitSave()">Save search</button>
                </div>
            </div>
        </div>
        <form id="save-search-form" method="POST" action="{{ route('saved-searches.store') }}" class="hidden">
            @csrf
            <input type="hidden" name="name" id="save-search-name-input">
            <input type="hidden" name="filters" id="filters-json-input">
        </form>
    @endauth
</div>
