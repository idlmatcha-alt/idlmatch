<x-guest-layout title="Step 6: Family Details">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-cta">Step 6 of 8</span>
            <span class="text-xs font-bold text-slate-400">75% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-cta transition-all duration-500" style="width: 75%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Family Details</h1>
        <p class="text-slate-500 mt-1">Tell us about your parents and siblings.</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-6.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Father's Name</label>
                <input type="text" name="father_name" value="{{ old('father_name', $profile->father_name) }}" 
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="Enter name">
                <x-input-error :messages="$errors->get('father_name')" class="mt-2" />
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Father's Occupation</label>
                <input type="text" name="father_occupation" value="{{ old('father_occupation', $profile->father_occupation) }}" 
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="Enter occupation">
                <x-input-error :messages="$errors->get('father_occupation')" class="mt-2" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Mother's Occupation</label>
            <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $profile->mother_occupation ?? 'Housewife') }}" 
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="Housewife">
            <x-input-error :messages="$errors->get('mother_occupation')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
             <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Family Values</label>
                <select name="family_values" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                    <option value="">Select Values</option>
                    @foreach(\App\Helpers\FormOptions::familyValues() as $option)
                        <option value="{{ $option }}" @selected(old('family_values', $profile->family_values) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('family_values')" class="mt-2" />
            </div>

             <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Family Type</label>
                <select name="family_type" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                    <option value="">Select Type</option>
                    @foreach(\App\Helpers\FormOptions::familyTypes() as $option)
                        <option value="{{ $option }}" @selected(old('family_type', $profile->family_type) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('family_type')" class="mt-2" />
            </div>

             <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Family Status</label>
                <select name="family_status" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                    <option value="">Select Status</option>
                    @foreach(\App\Helpers\FormOptions::familyStatuses() as $option)
                        <option value="{{ $option }}" @selected(old('family_status', $profile->family_status) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('family_status')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div x-data="{ count: {{ old('brothers_count', $profile->brothers_count ?? 0) }} }">
                <label class="block text-sm font-bold text-slate-700 mb-2 text-center uppercase tracking-tighter text-[10px]">Brothers</label>
                <div class="flex items-center">
                    <button type="button" @click="count = Math.max(0, count - 1)" class="w-10 h-10 rounded-l-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition-colors flex items-center justify-center border border-r-0 border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <input type="number" name="brothers_count" x-model="count" min="0"
                        class="block w-full px-2 py-3 border-slate-200 bg-slate-50/50 text-slate-900 text-center focus:border-cta focus:ring-cta/20 transition-all border-l-0 border-r-0 h-10 appearance-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" @click="count++" class="w-10 h-10 rounded-r-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition-colors flex items-center justify-center border border-l-0 border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('brothers_count')" class="mt-2" />
            </div>
            
            <div x-data="{ count: {{ old('sisters_count', $profile->sisters_count ?? 0) }} }">
                <label class="block text-sm font-bold text-slate-700 mb-2 text-center uppercase tracking-tighter text-[10px]">Sisters</label>
                <div class="flex items-center">
                    <button type="button" @click="count = Math.max(0, count - 1)" class="w-10 h-10 rounded-l-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition-colors flex items-center justify-center border border-r-0 border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <input type="number" name="sisters_count" x-model="count" min="0"
                        class="block w-full px-2 py-3 border-slate-200 bg-slate-50/50 text-slate-900 text-center focus:border-cta focus:ring-cta/20 transition-all border-l-0 border-r-0 h-10 appearance-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" @click="count++" class="w-10 h-10 rounded-r-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition-colors flex items-center justify-center border border-l-0 border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('sisters_count')" class="mt-2" />
            </div>

            <div x-data="{ count: {{ old('married_siblings_count', $profile->married_siblings_count ?? 0) }} }">
                <label class="block text-sm font-bold text-slate-700 mb-2 text-center uppercase tracking-tighter text-[10px]">Married</label>
                <div class="flex items-center">
                    <button type="button" @click="count = Math.max(0, count - 1)" class="w-10 h-10 rounded-l-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition-colors flex items-center justify-center border border-r-0 border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <input type="number" name="married_siblings_count" x-model="count" min="0"
                        class="block w-full px-2 py-3 border-slate-200 bg-slate-50/50 text-slate-900 text-center focus:border-cta focus:ring-cta/20 transition-all border-l-0 border-r-0 h-10 appearance-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" @click="count++" class="w-10 h-10 rounded-r-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition-colors flex items-center justify-center border border-l-0 border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('married_siblings_count')" class="mt-2" />
            </div>
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Continue to Address
            </button>
            <a href="{{ route('profile.wizard.step-5') }}" class="text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Go Back</a>
        </div>
    </form>
</x-guest-layout>


