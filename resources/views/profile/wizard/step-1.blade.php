<x-guest-layout title="Step 1: Additional Details">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-cta">Step 1 of 8</span>
            <span class="text-xs font-bold text-slate-400">12% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-cta transition-all duration-500" style="width: 12.5%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Additional Information</h1>
        <p class="text-slate-500 mt-1">Please provide a few more details to complete your profile.</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-1.store') }}" class="space-y-6">
        @csrf

        {{-- Hidden fields for required validation data that is already collected in registration --}}
        <input type="hidden" name="gender" value="{{ old('gender', $profile->gender) }}">
        <input type="hidden" name="marital_status" value="{{ old('marital_status', $profile->marital_status) }}">
        <input type="hidden" name="dob" value="{{ old('dob', $profile->dob ? $profile->dob->format('Y-m-d') : '') }}">
        <input type="hidden" name="height_cm" value="{{ old('height_cm', $profile->height_cm) }}">

        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Appearance & Lifestyle</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Complexion</label>
                    <select name="complexion" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select Complexion</option>
                        @foreach(\App\Helpers\FormOptions::complexions() as $option)
                            <option value="{{ $option }}" @selected(old('complexion', $profile->complexion) == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('complexion')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Body Type</label>
                    <select name="body_type" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select Body Type</option>
                        @foreach(\App\Helpers\FormOptions::bodyTypes() as $option)
                            <option value="{{ $option }}" @selected(old('body_type', $profile->body_type) == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('body_type')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Eating Habits</label>
                    <select name="eating_habits" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select One</option>
                        @foreach(\App\Helpers\FormOptions::eatingHabits() as $option)
                            <option value="{{ $option }}" @selected(old('eating_habits', $profile->eating_habits) == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('eating_habits')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Smoking Habits</label>
                    <select name="smoking_habits" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select One</option>
                        @foreach(\App\Helpers\FormOptions::smokingHabits() as $option)
                            <option value="{{ $option }}" @selected(old('smoking_habits', $profile->smoking_habits) == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('smoking_habits')" class="mt-2" />
                </div>
            </div>

            <div>
                 <label class="block text-sm font-bold text-slate-700 mb-2">Hobbies & Interests</label>
                 <textarea name="hobbies" rows="2" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all placeholder:text-slate-400" placeholder="Reading, Traveling, Cooking...">{{ old('hobbies', $profile->hobbies) }}</textarea>
                 <x-input-error :messages="$errors->get('hobbies')" class="mt-2" />
            </div>
        </div>

        <div class="space-y-4">
             <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Other Details</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Disability</label>
                    <select name="disability" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select One</option>
                        @foreach(\App\Helpers\FormOptions::disabilityOptions() as $option)
                            <option value="{{ $option }}" @selected(old('disability', $profile->disability) == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('disability')" class="mt-2" />
                </div>
    
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Mother Tongue</label>
                    <select name="mother_tongue" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select One</option>
                        @foreach(\App\Helpers\FormOptions::motherTongues() as $option)
                            <option value="{{ $option }}" @selected(old('mother_tongue', $profile->mother_tongue) == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('mother_tongue')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Continue to Education
            </button>
        </div>
    </form>
</x-guest-layout>
