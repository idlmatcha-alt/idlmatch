<x-auth-full-layout title="Partner Preferences">
    <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-brand-500 to-pink-500"></div>

            <div class="p-6 md:p-8">
                <!-- Header -->
                <div class="text-center mb-6">
                    <a href="/" class="inline-flex items-center gap-2 mb-3">
                        <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </div>
                        <span class="font-display font-bold text-xl text-slate-800">IDEAL MARRIAGE CENTER</span>
                    </a>
                    <h1 class="text-2xl font-bold text-slate-900">Partner Preferences</h1>
                    <p class="text-slate-500 mt-1">Tell us about your ideal life partner</p>

                    <!-- Progress -->
                    <div class="mt-4 flex items-center justify-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-brand-500">Step 3 of 3</span>
                        <span class="text-xs text-slate-400">•</span>
                        <span class="text-xs font-bold text-green-600">Final Step</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.preferences.store') }}" class="space-y-5">
                    @csrf

                    <!-- About Me Section -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-semibold text-brand-500 uppercase tracking-wide">About Me</h3>
                        <div class="bg-brand-50/50 rounded-xl p-4 border border-brand-100">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Write a short bio about yourself <span class="text-slate-400 font-normal">(Tell others who you are and what you're looking for)</span></label>
                            <textarea name="bio" rows="4" 
                                class="w-full rounded-xl border-slate-200 py-3 text-sm focus:border-brand-500 focus:ring-brand-500 transition-all"
                                placeholder="Example: I am a software engineer from Karachi. I love traveling, reading, and I'm looking for someone who shares similar values...">{{ old('bio', $profile->bio) }}</textarea>
                            @error('bio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Basic Preferences -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Basic Preferences</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Min Age</label>
                                <select name="desired_age_min"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    @for($i = 18; $i <= 60; $i++)
                                        <option value="{{ $i }}" {{ $i == 18 ? 'selected' : '' }}>{{ $i }} years</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Max Age</label>
                                <select name="desired_age_max"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    @for($i = 18; $i <= 70; $i++)
                                        <option value="{{ $i }}" {{ $i == 35 ? 'selected' : '' }}>{{ $i }} years</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Min Height</label>
                                <select name="desired_height_min"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    @for($h = 140; $h <= 200; $h += 2)
                                        @php $ft = floor($h / 30.48);
                                        $in = round(($h / 2.54) - ($ft * 12)); @endphp
                                        <option value="{{ $h }}" {{ $h == 150 ? 'selected' : '' }}>{{ $ft }}'{{ $in }}"
                                            ({{ $h }}cm)</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Max Height</label>
                                <select name="desired_height_max"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    @for($h = 140; $h <= 210; $h += 2)
                                        @php $ft = floor($h / 30.48);
                                        $in = round(($h / 2.54) - ($ft * 12)); @endphp
                                        <option value="{{ $h }}" {{ $h == 180 ? 'selected' : '' }}>{{ $ft }}'{{ $in }}"
                                            ({{ $h }}cm)</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Marital Status</label>
                                <select name="desired_marital_status"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    <option value="Single">Single (Never Married)</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Religion</label>
                                <select name="desired_religion"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($religions as $religion)
                                        <option value="{{ $religion }}">{{ $religion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Sect</label>
                                <select name="desired_sect"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($sects as $sect)
                                        <option value="{{ $sect }}">{{ $sect }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Caste</label>
                                <select name="desired_caste"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($castes as $caste)
                                        <option value="{{ $caste }}">{{ $caste }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Mother Tongue</label>
                                <select name="desired_mother_tongue"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($mother_tongues as $tongue)
                                        <option value="{{ $tongue }}">{{ $tongue }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Disability</label>
                                <select name="desired_disability"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    <option value="None" selected>None</option>
                                    <option value="Physical">Physical Disability</option>
                                    <option value="Mental">Mental Disability</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Religiousness</label>
                                <select name="desired_religiousness"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($religiousness_levels as $level)
                                        <option value="{{ $level }}" {{ ($preferences->desired_religiousness ?? '') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Living Arrangement</label>
                                <select name="desired_living_arrangement"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($living_arrangements as $option)
                                        <option value="{{ $option }}" {{ ($preferences->desired_living_arrangement ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Education & Career -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Education & Career</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Minimum Education</label>
                                <select name="desired_education"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($education_levels as $level)
                                        <option value="{{ $level }}">{{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Preferred Profession</label>
                                <select name="desired_profession"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any Profession</option>
                                    <!-- Options populated from helper or hardcoded? Using hardcoded for now based on snippet, but best to use helper if available -->
                                    <optgroup label="Medical & Healthcare">
                                        <option value="Doctor">Doctor</option>
                                        <option value="Dentist">Dentist</option>
                                        <option value="Nurse">Nurse</option>
                                        <option value="Pharmacist">Pharmacist</option>
                                    </optgroup>
                                    <optgroup label="Engineering & IT">
                                        <option value="Software Engineer">Software Engineer</option>
                                        <option value="Civil Engineer">Civil Engineer</option>
                                        <option value="Electrical Engineer">Electrical Engineer</option>
                                        <option value="IT Professional">IT Professional</option>
                                    </optgroup>
                                    <optgroup label="Business & Finance">
                                        <option value="Businessman">Businessman</option>
                                        <option value="Banker">Banker</option>
                                        <option value="Accountant">Accountant</option>
                                        <option value="CA">Chartered Accountant</option>
                                    </optgroup>
                                    <optgroup label="Education">
                                        <option value="Teacher">Teacher</option>
                                        <option value="Professor">Professor</option>
                                        <option value="Lecturer">Lecturer</option>
                                    </optgroup>
                                    <optgroup label="Government">
                                        <option value="Government Officer">Government Officer</option>
                                        <option value="Army Officer">Army/Military</option>
                                        <option value="Police Officer">Police Officer</option>
                                    </optgroup>
                                </select>
                            </div>

                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Job Type</label>
                                <input type="text" name="desired_job_type" placeholder="e.g. Govt, Private, Business" value="{{ $preferences->desired_job_type ?? '' }}"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Income</label>
                                <input type="text" name="desired_income" placeholder="e.g. 50k+" value="{{ $preferences->desired_income ?? '' }}"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Location Preference
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Country</label>
                                <select name="desired_country"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any Country</option>
                                    <option value="Pakistan" selected>Pakistan</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                    <option value="Canada">Canada</option>
                                    <option value="UAE">UAE</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Germany">Germany</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">City</label>
                                <select name="desired_city"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any City</option>
                                    <option value="Karachi">Karachi</option>
                                    <option value="Lahore">Lahore</option>
                                    <option value="Islamabad">Islamabad</option>
                                    <option value="Rawalpindi">Rawalpindi</option>
                                    <option value="Faisalabad">Faisalabad</option>
                                    <option value="Multan">Multan</option>
                                    <option value="Peshawar">Peshawar</option>
                                    <option value="Quetta">Quetta</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Willing to Relocate</label>
                                <select name="desired_willing_to_relocate"
                                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Any</option>
                                    @foreach($relocation_options as $option)
                                        <option value="{{ $option }}" {{ ($preferences->desired_willing_to_relocate ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Additional Notes</h3>
                        <textarea name="additional_preferences" rows="3"
                            class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500"
                            placeholder="Any other preferences you'd like to mention..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="pt-4 space-y-3">
                        <button type="submit"
                            class="w-full py-3 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl transition-all text-lg">
                            Save Preferences & Continue
                        </button>
                        <a href="{{ route('packages.index') }}"
                            class="block w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl transition-colors text-center">
                            Skip for Now
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-auth-full-layout>