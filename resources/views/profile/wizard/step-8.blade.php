<x-guest-layout title="Step 8: Partner Preferences">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Step 8 of 8</span>
            <span class="text-xs font-bold text-slate-400">95% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-brand-600 transition-all duration-500" style="width: 100%"></div>
        </div>
    </div>

    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-slate-900 font-display">Partner Preferences</h1>
        <p class="text-slate-500 mt-2">Tell us what you are looking for to get the best matches.</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-8.store') }}" class="space-y-6">
        @csrf

        <!-- Age & Height Row -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <label class="block text-sm font-bold text-slate-700">Preferred Age Range</label>
                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-xs font-bold text-slate-400 uppercase">Min</span>
                        <input type="number" name="desired_age_min" value="{{ old('desired_age_min', $preferences->desired_age_min ?? 22) }}" 
                               class="block w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-xs font-bold text-slate-400 uppercase">Max</span>
                        <input type="number" name="desired_age_max" value="{{ old('desired_age_max', $preferences->desired_age_max ?? 25) }}" 
                               class="block w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-bold text-slate-700">Preferred Height (cm)</label>
                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                         <span class="absolute inset-y-0 left-3 flex items-center text-xs font-bold text-slate-400 uppercase">Min</span>
                        <input type="number" name="desired_height_min" value="{{ old('desired_height_min', $preferences->desired_height_min) }}" 
                               class="block w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    </div>
                    <div class="relative">
                         <span class="absolute inset-y-0 left-3 flex items-center text-xs font-bold text-slate-400 uppercase">Max</span>
                        <input type="number" name="desired_height_max" value="{{ old('desired_height_max', $preferences->desired_height_max) }}" 
                               class="block w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Religion & Sect -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Religion Preference</label>
                <select name="desired_religion" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($religions as $religion)
                        <option value="{{ $religion }}" @selected(old('desired_religion', $preferences->desired_religion ?? '') == $religion)>{{ $religion }}</option>
                    @endforeach
                </select>
            </div>
             <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Sect Preference</label>
                <select name="desired_sect" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($sects as $sect)
                        <option value="{{ $sect }}" @selected(old('desired_sect', $preferences->desired_sect ?? '') == $sect)>{{ $sect }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Caste & Marital Status -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Caste Preference</label>
                <input type="text" name="desired_caste" value="{{ old('desired_caste', $preferences->desired_caste ?? 'Any') }}" list="desired-caste-options"
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm" 
                       placeholder="Select or Type...">
                <datalist id="desired-caste-options">
                    <option value="Any">
                    @foreach($castes as $caste)
                        <option value="{{ $caste }}">
                    @endforeach
                </datalist>
                <p class="mt-1 text-xs text-slate-500">Select from list or type to search</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Marital Status</label>
                <select name="desired_marital_status" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($marital_statuses as $status)
                        <option value="{{ $status }}" @selected(old('desired_marital_status', $preferences->desired_marital_status) == $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Location & Education -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">City Preference</label>
                <input type="text" name="desired_city" value="{{ old('desired_city', $preferences->desired_city) }}" list="desired-city-options"
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm" 
                       placeholder="e.g. Lahore">
                <datalist id="desired-city-options">
                    @foreach($cities as $city)
                        <option value="{{ $city }}">
                    @endforeach
                </datalist>
            </div>
             <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Education</label>
                <select name="desired_education" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($education_levels as $edu)
                        <option value="{{ $edu }}" @selected(old('desired_education', $preferences->desired_education) == $edu)>{{ $edu }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Country Preference</label>
                <input type="text" name="desired_country" value="{{ old('desired_country', $preferences->desired_country) }}"
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm"
                       placeholder="e.g. Pakistan">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">State Preference</label>
                <input type="text" name="desired_state" value="{{ old('desired_state', $preferences->desired_state) }}"
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm"
                       placeholder="e.g. Punjab">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Mother Tongue</label>
                <select name="desired_mother_tongue" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($mother_tongues as $tongue)
                        <option value="{{ $tongue }}" @selected(old('desired_mother_tongue', $preferences->desired_mother_tongue) == $tongue)>{{ $tongue }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Profession Preference</label>
                <input type="text" name="desired_profession" value="{{ old('desired_profession', $preferences->desired_profession) }}"
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm"
                       placeholder="e.g. Engineer, Doctor">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Religiousness</label>
                <select name="desired_religiousness" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($religiousness_levels as $level)
                        <option value="{{ $level }}" @selected(old('desired_religiousness', $preferences->desired_religiousness) == $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Willing to Relocate</label>
                <select name="desired_willing_to_relocate" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($relocation_options as $option)
                        <option value="{{ $option }}" @selected(old('desired_willing_to_relocate', $preferences->desired_willing_to_relocate) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Living Arrangement</label>
                <select name="desired_living_arrangement" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach($living_arrangements as $arrangement)
                        <option value="{{ $arrangement }}" @selected(old('desired_living_arrangement', $preferences->desired_living_arrangement) == $arrangement)>{{ $arrangement }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Income Preference</label>
                <input type="text" name="desired_income" value="{{ old('desired_income', $preferences->desired_income) }}"
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm"
                       placeholder="e.g. 80,000+">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Disability Preference</label>
            <select name="desired_disability" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                <option value="">Any</option>
                @foreach($disability_options as $option)
                    <option value="{{ $option }}" @selected(old('desired_disability', $preferences->desired_disability) == $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>

        <!-- Lifestyle Preferences -->
        <h3 class="text-lg font-bold text-slate-800 mt-6 mb-4 border-b pb-2">Lifestyle Preferences</h3>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Complexion Preference</label>
                <select name="desired_complexion" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach(\App\Helpers\FormOptions::complexions() as $option)
                        <option value="{{ $option }}" @selected(old('desired_complexion', $preferences->desired_complexion) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Body Type Preference</label>
                <select name="desired_body_type" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach(\App\Helpers\FormOptions::bodyTypes() as $option)
                        <option value="{{ $option }}" @selected(old('desired_body_type', $preferences->desired_body_type) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Eating Habits</label>
                <select name="desired_eating_habits" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach(\App\Helpers\FormOptions::eatingHabits() as $option)
                        <option value="{{ $option }}" @selected(old('desired_eating_habits', $preferences->desired_eating_habits) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Smoking Habits</label>
                <select name="desired_smoking_habits" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach(\App\Helpers\FormOptions::smokingHabits() as $option)
                        <option value="{{ $option }}" @selected(old('desired_smoking_habits', $preferences->desired_smoking_habits) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
             <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Family Values Preference</label>
                <select name="desired_family_values" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Any</option>
                    @foreach(\App\Helpers\FormOptions::familyValues() as $option)
                        <option value="{{ $option }}" @selected(old('desired_family_values', $preferences->desired_family_values) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Other Demands</label>
            <textarea name="other_requirements" rows="3"
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-white text-slate-900 focus:border-brand-500 focus:ring-brand-500 transition-all placeholder:text-slate-400 shadow-sm" 
                   placeholder="Any specific requests about family, lifestyle, etc...">{{ old('other_requirements', $preferences->other_requirements) }}</textarea>
        </div>

        <div class="pt-6 flex flex-col gap-3">
            <button type="submit" class="w-full py-4 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-lg font-bold shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Find My Matches
            </button>
            <a href="{{ route('profile.wizard.step-7') }}" class="text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Go Back</a>
        </div>
    </form>
</x-guest-layout>
