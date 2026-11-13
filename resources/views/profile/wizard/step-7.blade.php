<x-guest-layout title="Step 7: Address Details">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-cta">Step 7 of 8</span>
            <span class="text-xs font-bold text-slate-400">87% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-cta transition-all duration-500" style="width: 87.5%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Address & Contact</h1>
        <p class="text-slate-500 mt-1">Where do you live?</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-7.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Current City</label>
                <input type="text" name="city" value="{{ old('city', $profile->city ?? 'Lahore') }}" 
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Lahore">
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Home Town</label>
                <input type="text" name="home_town" value="{{ old('home_town', $profile->home_town) }}" 
                       class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Sialkot">
                <x-input-error :messages="$errors->get('home_town')" class="mt-2" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Location/Area</label>
            <input type="text" name="home_location" value="{{ old('home_location', $profile->home_location) }}" 
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Model Town, Block C">
            <x-input-error :messages="$errors->get('home_location')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Mobile Number</label>
            <div class="flex">
                <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-slate-200 bg-slate-100 text-slate-500 text-sm font-bold">+92</span>
                <input type="tel" name="mobile_no" value="{{ old('mobile_no', $profile->mobile_no) }}" 
                       class="block w-full px-4 py-3 rounded-r-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="300 1234567">
            </div>
            <x-input-error :messages="$errors->get('mobile_no')" class="mt-2" />
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Continue to Requirements
            </button>
            <a href="{{ route('profile.wizard.step-6') }}" class="text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Go Back</a>
        </div>
    </form>
</x-guest-layout>
