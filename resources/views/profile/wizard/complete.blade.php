@extends('layouts.app')

@section('header')
    <h2 class="font-display text-xl text-slate-900">
        Complete Profile Setup
    </h2>
@endsection

@section('content')
<div class="min-h-screen bg-slate-50 pb-20" x-data="{ 
    editing: {},
    toggleEdit(field) {
        this.editing[field] = !this.editing[field];
    }
}">
    <div class="max-w-5xl mx-auto px-4 pt-10">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 mb-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Complete Setup</p>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">Complete your profile</h1>
                    <p class="mt-1 text-sm text-slate-600">Fill in missing fields to improve matches and visibility.</p>
                </div>
                <div class="min-w-[180px]">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                        <span>Profile strength</span>
                        <span>{{ $profile->completion_score }}%</span>
                    </div>
                    <div class="mt-2 h-2 w-full rounded-full bg-slate-100">
                        <div class="h-2 rounded-full bg-brand-600 transition-all duration-700" style="width: {{ $profile->completion_score }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.complete.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="space-y-8">
                {{-- Profile Picture Section --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800 leading-tight">Profile Picture</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-[0.2em] mt-1">Upload a clear photo of yourself</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-shrink-0">
                                <div class="relative group">
                                    <div class="h-32 w-32 rounded-2xl overflow-hidden bg-slate-100 border-2 border-slate-200">
                                        @if($profile->primaryPhoto)
                                            <img src="{{ asset('storage/' . $profile->primaryPhoto->path) }}" alt="Profile" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-slate-300">
                                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow w-full">
                                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Upload New Photo</label>
                                <div class="flex flex-col sm:flex-row items-center gap-3">
                                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-all border border-slate-200 rounded-xl p-1">
                                    <button type="submit" name="save_photo" value="1" class="w-full sm:w-auto px-8 py-4 bg-brand-600 hover:bg-brand-700 text-white text-[11px] font-bold uppercase tracking-[0.2em] rounded-xl transition shadow-sm whitespace-nowrap">
                                        Save Photo
                                    </button>
                                </div>
                                <p class="mt-2 text-[10px] text-slate-400">JPG, PNG or GIF. Max size 10MB.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach($sections as $title => $section)
                    <div id="section-{{ \Illuminate\Support\Str::slug($title) }}" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                    @if($section['icon'] == 'user')
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    @elseif($section['icon'] == 'identity')
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            @elseif($section['icon'] == 'academic')
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            @elseif($section['icon'] == 'users')
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            @elseif($section['icon'] == 'location')
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            @elseif($section['icon'] == 'home')
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            @endif
                        </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800 leading-tight">{{ $title }}</h2>
                                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-[0.2em] mt-1">Section Details & Updates</p>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid md:grid-cols-2 gap-y-6 gap-x-8">
                                    @foreach($section['fields'] as $field => $config)
                                        @php
                                            $source = $config['source'] ?? 'profile';
                                            if ($source === 'preferences') {
                                                $sourceModel = $profile->preferences ?? null;
                                                $value = $sourceModel?->{$field};
                                            } elseif ($source === 'user') {
                                                $sourceModel = auth()->user();
                                                $value = $sourceModel?->{$field};
                                            } elseif ($source === 'privacy') {
                                                $sourceModel = null;
                                                $value = (string) ((int) data_get((array) ($profile->privacy_json ?? []), $field, 0));
                                            } else {
                                                $sourceModel = $profile;
                                                $value = $sourceModel?->{$field};
                                            }

                                            $stringValue = is_array($value)
                                                ? collect($value)->map(fn ($item) => trim((string) $item))->filter(fn ($item) => $item !== '')->implode(', ')
                                                : $value;
                                            $selectLookupValue = is_bool($value) ? (string) ((int) $value) : (string) $stringValue;
                                            $inputValue = ($config['type'] ?? 'text') === 'date' && $value instanceof \Carbon\CarbonInterface
                                                ? $value->format('Y-m-d')
                                                : $stringValue;
                                            $isFilled = !empty($stringValue) || $stringValue === 0 || $stringValue === '0';
                                        @endphp
                                        <div class="relative group/item">
                                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">{{ $config['label'] }}</label>
                                            
                                            <div x-show="!editing['{{ $field }}'] && {{ $isFilled ? 'true' : 'false' }}" 
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="group flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-slate-200 hover:bg-white transition-all cursor-pointer"
                                                 @click="toggleEdit('{{ $field }}')">
                                                
                                                <div class="flex flex-col">
                                                    <span class="text-slate-800 font-semibold text-[15px]">
                                                        @if($config['type'] == 'select' && isset($config['options'][$selectLookupValue]))
                                                            {{ $config['options'][$selectLookupValue] }}
                                                        @elseif($config['type'] == 'select' && is_array($config['options']) && !isset($config['options'][$selectLookupValue]))
                                                             {{ $stringValue }}
                                                        @elseif($config['type'] == 'date' && $value instanceof \Carbon\CarbonInterface)
                                                            {{ $value->format('d M Y') }}
                                                        @else
                                                            {{ $stringValue ?: 'Not Specified' }}
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center gap-2">
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-50 text-green-500 @if(!$value) hidden @endif">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                                    </span>
                                                    <span class="text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity p-2 rounded-lg bg-slate-100">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                    </span>
                                                </div>
                                            </div>

                                            <div x-show="editing['{{ $field }}'] || !{{ $isFilled ? 'true' : 'false' }}"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                         x-cloak>
                                        @if($config['type'] == 'textarea')
                                                    <textarea name="{{ $field }}" rows="3" class="form-textarea w-full rounded-xl border-slate-200 p-4 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-slate-700" placeholder="{{ $config['placeholder'] ?? 'Add details...' }}">{{ old($field, $inputValue) }}</textarea>
                                        @elseif($config['type'] == 'select')
                                                        <select name="{{ $field }}" id="{{ $field }}Select" class="form-select w-full rounded-xl border-slate-200 p-4 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-slate-800 appearance-none">
                                                            <option value="">Select {{ $config['label'] }}</option>
                                                            @foreach($config['options'] as $key => $label)
                                                                @if(is_array($label))
                                                                    <optgroup label="{{ $key }}">
                                                                        @foreach($label as $subKey => $subLabel)
                                                                            @php $optVal = is_numeric($subKey) ? $subLabel : $subKey; @endphp
                                                                            <option value="{{ $optVal }}" @selected(old($field, $value) == $optVal)>{{ $subLabel }}</option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @else
                                                                    @php $optVal = is_numeric($key) ? $label : $key; @endphp
                                                                    <option value="{{ $optVal }}" @selected(old($field, $value) == $optVal)>{{ $label }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @if(in_array($field, ['sect', 'caste', 'major_course', 'profession', 'country', 'state', 'city']))
                                                            <input type="text" id="{{ $field }}OtherInput" placeholder="Specify (Optional)" class="hidden w-full mt-2 rounded-xl border-slate-200 p-4 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-slate-700">
                                                        @endif
                                        @else
                                                    <input type="{{ $config['type'] }}" name="{{ $field }}" value="{{ old($field, $inputValue) }}" class="form-input w-full rounded-xl border-slate-200 p-4 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-slate-800" placeholder="{{ $config['placeholder'] ?? 'Add info...' }}">
                                        @endif
                                        
                                        @if($isFilled)
                                            <div class="flex justify-end mt-2">
                                                    <button type="button" @click="toggleEdit('{{ $field }}')" class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600 transition flex items-center gap-1.5 p-1">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        Cancel Edit
                                                    </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Final CTA -->
            <div class="relative pt-10">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-6 py-2 rounded-full border border-slate-200 text-xs font-semibold text-slate-400 uppercase tracking-[0.2em]">Ready to update?</span>
                </div>
            </div>

            <div class="pb-24">
                <button type="submit" class="group relative w-full py-5 rounded-2xl bg-brand-600 hover:bg-brand-700 transition-all">
                    <div class="flex items-center justify-center gap-4">
                        <span class="text-white text-base md:text-lg font-semibold tracking-[0.2em] uppercase">Finalize My Profile</span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    .font-display { font-family: 'Playfair Display', serif; }
    .font-sans { font-family: 'Manrope', sans-serif; }

    .form-input, .form-textarea, .form-select {
        @apply bg-white transition-all duration-300;
    }

    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out forwards;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const religionSects = {
        'Islam': ['Sunni', 'Shia', 'Sufi', 'Other'],
        'Christianity': ['Catholic', 'Protestant', 'Orthodox', 'Other'],
        'Hinduism': ['Vaishnavism', 'Shaivism', 'Other'],
        'Buddhism': ['Theravada', 'Mahayana', 'Other'],
        'Sikhism': ['Khalsa', 'Other'],
        'Other': ['Other']
    };

    const religionSelect = document.getElementById('religionSelect');
    const sectSelect = document.getElementById('sectSelect');

    const qualificationMajors = @json(\App\Helpers\FormOptions::majorsByQualification());
    const educationSelect = document.getElementById('educationSelect');
    const majorSelect = document.getElementById('major_courseSelect');

    function updateMajors(qualification, currentMajor = '') {
        if (!majorSelect) return;
        const majors = qualificationMajors[qualification] || ['Other'];
        const previousValue = currentMajor || majorSelect.value;
        majorSelect.innerHTML = '<option value="">Select Major...</option>';
        majors.forEach(major => {
            const option = new Option(major, major);
            if (major === previousValue) option.selected = true;
            majorSelect.add(option);
        });
    }

    if (educationSelect) {
        educationSelect.addEventListener('change', () => updateMajors(educationSelect.value));
        if (educationSelect.value) {
            updateMajors(educationSelect.value, "{{ $profile->major_course }}");
        }
    }

    function updateSects(religion, currentSect = '') {
        if (!sectSelect) return;
        const sects = religionSects[religion] || ['Other'];
        const previousValue = currentSect || sectSelect.value;
        sectSelect.innerHTML = '<option value="">Select Sect...</option>';
        sects.forEach(sect => {
            const option = new Option(sect, sect);
            if (sect === previousValue) option.selected = true;
            sectSelect.add(option);
        });
    }

    if (religionSelect) {
        religionSelect.addEventListener('change', () => updateSects(religionSelect.value));
        // Initialize if religion is already selected
        if (religionSelect.value) {
            updateSects(religionSelect.value, "{{ $profile->sect }}");
        }
    }

    // Dynamic Country -> State/Province -> City dropdowns
    const countrySelect = document.getElementById('countrySelect');
    const stateSelect = document.getElementById('stateSelect');
    const citySelect = document.getElementById('citySelect');
    let initialCountry = @json((string) ($profile->country ?? ''));
    let initialState = @json((string) ($profile->state ?? ''));
    let initialCity = @json((string) ($profile->city ?? ''));

    function ensureOption(select, value) {
        if (!select || !value) return;
        const hasOption = Array.from(select.options).some((option) => option.value === value);
        if (!hasOption) {
            select.add(new Option(value, value));
        }
    }

    async function fetchCountries() {
        if (!countrySelect) return;
        countrySelect.innerHTML = '<option value="">Loading countries...</option>';

        try {
            const response = await fetch('/locations/countries');
            const data = await response.json();

            countrySelect.innerHTML = '<option value="">Select Country...</option>';
            (data || []).forEach((country) => {
                countrySelect.add(new Option(country.name, country.name));
            });

            countrySelect.add(new Option('Other', 'Other'));

            const selectedCountry = initialCountry || countrySelect.value;
            if (selectedCountry) {
                ensureOption(countrySelect, selectedCountry);
                countrySelect.value = selectedCountry;
                if (selectedCountry !== 'Other') {
                    await fetchStates(selectedCountry);
                }
            }
            initialCountry = '';
        } catch (error) {
            countrySelect.innerHTML = '<option value="">Select Country...</option>';
            if (initialCountry) {
                ensureOption(countrySelect, initialCountry);
                countrySelect.value = initialCountry;
            }
        }
    }

    async function fetchStates(countryName) {
        if (!stateSelect || !citySelect) return;
        if (!countryName || countryName === 'Other') {
            stateSelect.innerHTML = '<option value="">Select State/Province...</option><option value="Other">Other</option>';
            citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
            return;
        }

        stateSelect.innerHTML = '<option value="">Loading states...</option>';
        citySelect.innerHTML = '<option value="">Select City...</option>';

        try {
            const response = await fetch(`/locations/states/${encodeURIComponent(countryName)}`);
            const data = await response.json();

            stateSelect.innerHTML = '<option value="">Select State/Province...</option>';
            (data || []).forEach((state) => {
                const option = new Option(state.name, state.name);
                option.dataset.id = String(state.id);
                stateSelect.add(option);
            });

            stateSelect.add(new Option('Other', 'Other'));

            const selectedState = initialState || stateSelect.value;
            if (selectedState) {
                ensureOption(stateSelect, selectedState);
                stateSelect.value = selectedState;
                const matched = Array.from(stateSelect.options).find((o) => o.value === selectedState);
                if (matched?.dataset?.id) {
                    await fetchCities(matched.dataset.id);
                } else {
                    ensureOption(citySelect, initialCity);
                    if (initialCity) {
                        citySelect.value = initialCity;
                    }
                }
            }
            initialState = '';
        } catch (error) {
            stateSelect.innerHTML = '<option value="">Select State/Province...</option><option value="Other">Other</option>';
            if (initialState) {
                ensureOption(stateSelect, initialState);
                stateSelect.value = initialState;
            }
        }
    }

    async function fetchCities(stateId) {
        if (!citySelect) return;
        if (!stateId) {
            citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
            return;
        }

        citySelect.innerHTML = '<option value="">Loading cities...</option>';

        try {
            const response = await fetch(`/locations/cities/${stateId}`);
            const data = await response.json();

            citySelect.innerHTML = '<option value="">Select City...</option>';
            (data || []).forEach((city) => {
                citySelect.add(new Option(city.name, city.name));
            });
            citySelect.add(new Option('Other', 'Other'));

            const selectedCity = initialCity || citySelect.value;
            if (selectedCity) {
                ensureOption(citySelect, selectedCity);
                citySelect.value = selectedCity;
            }
            initialCity = '';
        } catch (error) {
            citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
            if (initialCity) {
                ensureOption(citySelect, initialCity);
                citySelect.value = initialCity;
            }
        }
    }

    if (countrySelect && stateSelect && citySelect) {
        fetchCountries();

        countrySelect.addEventListener('change', async function () {
            if (this.value === 'Other') {
                stateSelect.innerHTML = '<option value="">Select State/Province...</option><option value="Other">Other</option>';
                citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                return;
            }

            await fetchStates(this.value);
        });

        stateSelect.addEventListener('change', async function () {
            if (this.value === 'Other') {
                citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                return;
            }

            const selectedOption = this.options[this.selectedIndex];
            const stateId = selectedOption?.dataset?.id;
            if (stateId) {
                await fetchCities(stateId);
            } else {
                citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
            }
        });
    }

    // Other Toggle Setup
    function setupOtherToggle(selectId, inputId) {
        const select = document.getElementById(selectId);
        const input = document.getElementById(inputId);
        if (!select || !input) return;
        
        const checkToggle = () => {
            const isOther = select.value === 'Other' || (select.value && !Array.from(select.options).some(o => o.value === select.value && o.value !== 'Other'));
            input.classList.toggle('hidden', !isOther);
        };

        select.addEventListener('change', function() {
            input.classList.toggle('hidden', this.value !== 'Other');
            if (this.value === 'Other') input.focus();
        });
        
        checkToggle();
    }

    setupOtherToggle('religionSelect', 'religionOtherInput');
    setupOtherToggle('sectSelect', 'sectOtherInput');
    setupOtherToggle('casteSelect', 'casteOtherInput');
    setupOtherToggle('major_courseSelect', 'major_courseOtherInput');
    setupOtherToggle('professionSelect', 'professionOtherInput');
    setupOtherToggle('countrySelect', 'countryOtherInput');
    setupOtherToggle('stateSelect', 'stateOtherInput');
    setupOtherToggle('citySelect', 'cityOtherInput');

    // Handle Other values on submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const pairs = [
            { select: 'religionSelect', input: 'religionOtherInput' },
            { select: 'sectSelect', input: 'sectOtherInput' },
            { select: 'casteSelect', input: 'casteOtherInput' },
            { select: 'major_courseSelect', input: 'major_courseOtherInput' },
            { select: 'professionSelect', input: 'professionOtherInput' },
            { select: 'countrySelect', input: 'countryOtherInput' },
            { select: 'stateSelect', input: 'stateOtherInput' },
            { select: 'citySelect', input: 'cityOtherInput' }
        ];
        pairs.forEach(pair => {
            const s = document.getElementById(pair.select);
            const i = document.getElementById(pair.input);
            if (s && i && s.value === 'Other' && i.value.trim() !== '') {
                s.options[s.selectedIndex].value = i.value.trim();
            }
        });
    });
});
</script>
@endsection
