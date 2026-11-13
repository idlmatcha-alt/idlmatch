<x-guest-layout title="Step 2: Education Details">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-cta">Step 2 of 8</span>
            <span class="text-xs font-bold text-slate-400">25% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-cta transition-all duration-500" style="width: 25%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Education & Career</h1>
        <p class="text-slate-500 mt-1">Tell us about your academic and professional background.</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-2.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Highest Qualification</label>
            <input type="text" name="education" value="{{ old('education', $profile->education) }}" 
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Masters in Computer Science">
            <x-input-error :messages="$errors->get('education')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">College</label>
            <input type="text" name="college" value="{{ old('college', $profile->college) }}" 
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Government College Lahore">
            <x-input-error :messages="$errors->get('college')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">University</label>
            <input type="text" name="university" value="{{ old('university', $profile->university) }}" 
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Punjab University">
            <x-input-error :messages="$errors->get('university')" class="mt-2" />
        </div>

        <div class="border-t border-slate-200 pt-6 mt-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Career Details</h3>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Profession</label>
                    <select name="profession" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select Profession</option>
                        @foreach(\App\Helpers\FormOptions::professions() as $group => $professions)
                            <optgroup label="{{ $group }}">
                                @foreach($professions as $prof)
                                    <option value="{{ $prof }}" @selected(old('profession', $profile->profession) == $prof)>{{ $prof }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('profession')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Job Title / Type</label>
                    <input type="text" name="job_type" value="{{ old('job_type', $profile->job_type) }}" 
                           class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Senior Manager / Full-time">
                    <x-input-error :messages="$errors->get('job_type')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Monthly Income</label>
                    <select name="income" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                        <option value="">Select Income...</option>
                        @foreach(\App\Helpers\FormOptions::incomeOptions() as $opt)
                            <option value="{{ $opt }}" @selected(old('income', $profile->income) == $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('income')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Continue to Verification
            </button>
            <a href="{{ route('profile.wizard.step-1') }}" class="text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Go Back</a>
        </div>
    </form>
</x-guest-layout>
