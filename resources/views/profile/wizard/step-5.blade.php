<x-guest-layout title="Step 5: Property Details">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-cta">Step 5 of 8</span>
            <span class="text-xs font-bold text-slate-400">62% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-cta transition-all duration-500" style="width: 62.5%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Property Details</h1>
        <p class="text-slate-500 mt-1">Living situation and assets.</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-5.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Home Status</label>
            <select name="home_status" class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all">
                <option value="own" @selected(old('home_status', $profile->home_status) == 'own')>Own House</option>
                <option value="rented" @selected(old('home_status', $profile->home_status) == 'rented')>Rented</option>
                <option value="sharing" @selected(old('home_status', $profile->home_status) == 'sharing')>Family Sharing</option>
            </select>
            <x-input-error :messages="$errors->get('home_status')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Home Size</label>
                <input type="text" name="home_size" value="{{ old('home_size', $profile->home_size) }}" 
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. 10 Marla">
                <x-input-error :messages="$errors->get('home_size')" class="mt-2" />
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Location</label>
                <input type="text" name="home_location" value="{{ old('home_location', $profile->home_location) }}" 
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. DHA Phase 5">
                <x-input-error :messages="$errors->get('home_location')" class="mt-2" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Other Properties (if any)</label>
            <textarea name="other_properties" rows="3"
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all placeholder:text-slate-400" 
                   placeholder="Commercial shops, agricultural land etc...">{{ old('other_properties', $profile->other_properties) }}</textarea>
            <x-input-error :messages="$errors->get('other_properties')" class="mt-2" />
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Continue to Family
            </button>
            <a href="{{ route('profile.wizard.step-4') }}" class="text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Go Back</a>
        </div>
    </form>
</x-guest-layout>
