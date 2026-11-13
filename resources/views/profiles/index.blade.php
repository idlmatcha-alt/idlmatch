@extends('layouts.marketing')


@section('content')
    @php
        $filterLabels = [
            'keyword' => 'Keyword',
            'display_id' => 'Profile ID',
            'gender' => 'Gender',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'sect' => 'Sect',
            'religion' => 'Religion',
            'caste' => 'Caste',
            'mother_tongue' => 'Mother tongue',
            'marital_status' => 'Marital status',
            'education' => 'Education',
            'profession' => 'Profession',
            'age_min' => 'Minimum age',
            'age_max' => 'Maximum age',
            'height_min' => 'Minimum height',
            'height_max' => 'Maximum height',
            'plan' => 'Membership',
        ];
        $booleanFilterLabels = [
            'verified_only' => 'Verified profiles',
            'second_marriage' => 'Second marriage',
            'divorced' => 'Divorced profiles',
        ];
        $activeFilters = [];

        foreach ($filters as $key => $value) {
            if ($key === 'sort' || $value === null || $value === '' || $value === []) {
                continue;
            }

            $values = is_array($value) ? $value : [$value];
            foreach ($values as $singleValue) {
                $query = request()->query();
                unset($query['page']);

                if (is_array($value)) {
                    $remaining = array_values(array_filter($query[$key] ?? [], fn ($item) => (string) $item !== (string) $singleValue));
                    if ($remaining === []) {
                        unset($query[$key]);
                    } else {
                        $query[$key] = $remaining;
                    }
                } else {
                    unset($query[$key]);
                }

                $label = $booleanFilterLabels[$key]
                    ?? (($filterLabels[$key] ?? ucwords(str_replace('_', ' ', $key))) . ': ' . $singleValue);
                if (in_array($key, ['height_min', 'height_max'], true)) {
                    $label .= ' cm';
                }

                $activeFilters[] = [
                    'label' => $label,
                    'url' => route('profiles.index', $query),
                ];
            }
        }
    @endphp

    <style>
        body { background: #f8f6f7; }
        .search-page { padding: 34px 0 64px; color: #273444; font-family: "Manrope", sans-serif; }
        .search-shell { width: min(1320px, calc(100% - 40px)); margin: 0 auto; }
        .search-intro { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin-bottom: 26px; }
        .search-eyebrow { margin: 0 0 7px; color: #a12d4d; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .search-title { margin: 0; color: #30232a; font-family: "Playfair Display", serif; font-size: clamp(30px, 3vw, 40px); line-height: 1.15; }
        .search-subtitle { max-width: 650px; margin: 9px 0 0; color: #6f646a; font-size: 14px; line-height: 1.7; }
        .search-trust { display: flex; align-items: center; gap: 9px; padding: 11px 15px; border: 1px solid #eadde1; border-radius: 12px; background: #fff; color: #68565e; font-size: 12px; font-weight: 700; white-space: nowrap; }
        .search-trust svg { width: 18px; height: 18px; color: #a12d4d; }
        .search-mobile-bar { display: none; }
        .search-layout { display: grid; grid-template-columns: 292px minmax(0, 1fr); align-items: start; gap: 24px; }
        .search-sidebar { min-width: 0; }
        .search-results { min-width: 0; }
        .search-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; min-height: 74px; margin-bottom: 16px; padding: 16px 20px; border: 1px solid #e9e2e5; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px rgba(64, 37, 47, .04); }
        .search-result-count { margin: 0; color: #3d3036; font-size: 16px; font-weight: 800; }
        .search-result-range { margin: 4px 0 0; color: #8a7b82; font-size: 12px; }
        .search-sort { display: flex; align-items: center; gap: 9px; flex-shrink: 0; }
        .search-sort label { color: #81747a; font-size: 12px; font-weight: 700; }
        .search-sort select { min-width: 156px; height: 39px; padding: 0 34px 0 12px; border: 1px solid #ded5d9; border-radius: 9px; background: #fff; color: #493b42; font-size: 12px; font-weight: 700; outline: none; }
        .search-sort select:focus { border-color: #ad4965; box-shadow: 0 0 0 3px rgba(161, 45, 77, .09); }
        .search-active { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 0 0 18px; }
        .search-active-label { margin-right: 2px; color: #81747a; font-size: 12px; font-weight: 700; }
        .search-chip { display: inline-flex; align-items: center; gap: 7px; max-width: 100%; padding: 7px 10px; border: 1px solid #e5cfd6; border-radius: 999px; background: #fff; color: #87324b; font-size: 11px; font-weight: 700; text-decoration: none; }
        .search-chip:hover { border-color: #c893a4; background: #fff8fa; }
        .search-chip span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .search-chip svg { width: 13px; height: 13px; flex-shrink: 0; }
        .search-clear { margin-left: 3px; color: #9d2b48; font-size: 11px; font-weight: 800; text-decoration: underline; text-underline-offset: 3px; }
        .search-results-list { display: grid; gap: 16px; }
        .search-results-list .idm-card { margin: 0; }
        .search-empty { padding: 64px 24px; border: 1px solid #e9e2e5; border-radius: 16px; background: #fff; text-align: center; }
        .search-empty-icon { display: inline-flex; align-items: center; justify-content: center; width: 62px; height: 62px; border-radius: 50%; background: #f8edf0; color: #a12d4d; }
        .search-empty-icon svg { width: 28px; height: 28px; }
        .search-empty h2 { margin: 18px 0 7px; color: #3d3036; font-family: "Playfair Display", serif; font-size: 25px; }
        .search-empty p { max-width: 420px; margin: 0 auto; color: #81747a; font-size: 13px; line-height: 1.7; }
        .search-empty a { display: inline-flex; margin-top: 20px; padding: 11px 18px; border-radius: 9px; background: #9d2b48; color: #fff; font-size: 12px; font-weight: 800; text-decoration: none; }
        .search-pagination { margin-top: 28px; }
        .search-errors { margin-bottom: 18px; padding: 13px 16px; border: 1px solid #efc7d1; border-radius: 10px; background: #fff4f6; color: #92233f; font-size: 13px; }
        @media (max-width: 1023px) {
            .search-layout { grid-template-columns: 1fr; }
            .search-sidebar { display: none; }
            .search-sidebar.is-open { display: block; }
            .search-mobile-bar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
            .search-filter-toggle { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; height: 45px; border: 1px solid #decdd3; border-radius: 10px; background: #fff; color: #8f2b47; font-size: 13px; font-weight: 800; }
            .search-filter-toggle svg { width: 18px; height: 18px; }
            .search-filter-count { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 5px; border-radius: 999px; background: #9d2b48; color: #fff; font-size: 10px; }
        }
        @media (max-width: 640px) {
            .search-page { padding: 24px 0 44px; }
            .search-shell { width: min(100% - 28px, 1320px); }
            .search-intro { display: block; margin-bottom: 20px; }
            .search-trust { display: none; }
            .search-subtitle { font-size: 13px; }
            .search-toolbar { align-items: flex-start; flex-direction: column; gap: 13px; padding: 15px; }
            .search-sort { width: 100%; justify-content: space-between; }
            .search-sort select { flex: 1; }
            .search-active { align-items: flex-start; }
            .search-active-label { width: 100%; }
        }
    </style>

    <section class="search-page" x-data="{ filtersOpen: false }">
        <div class="search-shell">
            <div class="search-intro">
                <div>
                    <p class="search-eyebrow">Find your match</p>
                    <h1 class="search-title">Search profiles</h1>
                    <p class="search-subtitle">Explore verified members and refine your preferences to find a compatible match for yourself or your family.</p>
                </div>
                <div class="search-trust">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l7 3v5c0 4.6-2.9 8.2-7 10-4.1-1.8-7-5.4-7-10V6l7-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4"/></svg>
                    Privacy-first matchmaking
                </div>
            </div>

            @if($errors->any())
                <div class="search-errors" role="alert">{{ $errors->first() }}</div>
            @endif

            <div class="search-mobile-bar">
                <button type="button" class="search-filter-toggle" @click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen.toString()" aria-controls="search-sidebar">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M7 12h10M10 18h4"/></svg>
                    <span x-text="filtersOpen ? 'Hide search filters' : 'Show search filters'">Show search filters</span>
                    @if(count($activeFilters))
                        <span class="search-filter-count">{{ count($activeFilters) }}</span>
                    @endif
                </button>
            </div>

            <div class="search-layout">
                <aside class="search-sidebar" :class="{ 'is-open': filtersOpen }" id="search-sidebar" aria-label="Search filters">
                    @include('profiles.partials.filters-form', ['stacked' => true, 'activeFilterCount' => count($activeFilters)])
                </aside>

                <main class="search-results">
                    <div class="search-toolbar">
                        <div>
                            <h2 class="search-result-count">{{ number_format($profiles->total()) }} {{ \Illuminate\Support\Str::plural('profile', $profiles->total()) }} found</h2>
                            @if($profiles->total())
                                <p class="search-result-range">Showing {{ $profiles->firstItem() }}–{{ $profiles->lastItem() }} of {{ $profiles->total() }} matches</p>
                            @else
                                <p class="search-result-range">Try broadening your preferences</p>
                            @endif
                        </div>
                        <form action="{{ route('profiles.index') }}" method="GET" class="search-sort">
                            @foreach(request()->except('sort', 'page') as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label for="search-sort">Sort by</label>
                            <select name="sort" id="search-sort" onchange="this.form.submit()">
                                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest profiles</option>
                                <option value="recent" @selected(request('sort') === 'recent')>Recently active</option>
                            </select>
                        </form>
                    </div>

                    @if($activeFilters !== [])
                        <div class="search-active" aria-label="Active filters">
                            <span class="search-active-label">Active filters</span>
                            @foreach($activeFilters as $activeFilter)
                                <a href="{{ $activeFilter['url'] }}" class="search-chip" title="Remove this filter">
                                    <span>{{ $activeFilter['label'] }}</span>
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endforeach
                            <a href="{{ route('profiles.index') }}" class="search-clear">Clear all</a>
                        </div>
                    @endif

                    <div class="search-results-list">
                        @forelse($profiles as $profile)
                            <x-profile-card :profile="$profile" />
                        @empty
                            <div class="search-empty">
                                <span class="search-empty-icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M21 21l-4.35-4.35m2.35-5.15a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"/></svg>
                                </span>
                                <h2>No exact matches yet</h2>
                                <p>Try removing one or two filters, widening the age range, or searching in another city.</p>
                                @if($activeFilters !== [])
                                    <a href="{{ route('profiles.index') }}">Reset all filters</a>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    @if($profiles->hasPages())
                        <div class="search-pagination">{{ $profiles->links() }}</div>
                    @endif
                </main>
            </div>
        </div>
    </section>
@endsection
