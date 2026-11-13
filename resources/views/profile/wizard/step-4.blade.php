<x-guest-layout title="Step 4: Religion Details">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-cta">Step 4 of 8</span>
            <span class="text-xs font-bold text-slate-400">50% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-cta transition-all duration-500" style="width: 50%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Religion & Roots</h1>
        <p class="text-slate-500 mt-1">Cultural and religious background.</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-4.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Religion</label>
            <input type="text" name="religion" value="{{ old('religion', $profile->religion ?? 'Islam') }}" 
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="Islam">
            <x-input-error :messages="$errors->get('religion')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Sect</label>
            <input type="text" name="sect" value="{{ old('sect', $profile->sect) }}" 
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" placeholder="e.g. Sunni">
            <x-input-error :messages="$errors->get('sect')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Caste</label>
            <input type="text" name="caste" value="{{ old('caste', $profile->caste) }}" list="caste-options"
                   class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all" 
                   placeholder="Type to search or select...">
            <datalist id="caste-options">
                <option value="Arain">
                <option value="Jat">
                <option value="Rajput">
                <option value="Gujjar">
                <option value="Awan">
                <option value="Malik">
                <option value="Sheikh">
                <option value="Syed">
                <option value="Mughal">
                <option value="Pathan">
                <option value="Baloch">
                <option value="Butt">
                <option value="Chaudhry">
                <option value="Khokhar">
                <option value="Khatri">
                <option value="Ansari">
                <option value="Qureshi">
                <option value="Abbasi">
                <option value="Hashmi">
                <option value="Gardezi">
                <option value="Gillani">
                <option value="Naqvi">
                <option value="Rizvi">
                <option value="Kazmi">
                <option value="Zaidi">
                <option value="Alvi">
                <option value="Farooqi">
                <option value="Usmani">
                <option value="Siddiqi">
                <option value="Osmani">
                <option value="Lodhi">
                <option value="Niazi">
                <option value="Durrani">
                <option value="Yousafzai">
                <option value="Afridi">
                <option value="Khattak">
                <option value="Marwat">
                <option value="Bangash">
                <option value="Orakzai">
                <option value="Wazir">
                <option value="Mehsud">
                <option value="Kakar">
                <option value="Achakzai">
                <option value="Rind">
                <option value="Bugti">
                <option value="Marri">
                <option value="Mengal">
                <option value="Jamali">
                <option value="Leghari">
                <option value="Khosa">
                <option value="Noon">
                <option value="Tiwana">
                <option value="Hayat">
                <option value="Warraich">
                <option value="Cheema">
                <option value="Virk">
                <option value="Sandhu">
                <option value="Gill">
                <option value="Bajwa">
                <option value="Bhatti">
                <option value="Dogar">
                <option value="Tarar">
                <option value="Minhas">
                <option value="Kashmiri">
                <option value="Dar">
                <option value="Lone">
                <option value="Mir">
                <option value="Khan">
                <option value="Other">
            </datalist>
            <x-input-error :messages="$errors->get('caste')" class="mt-2" />
            <p class="mt-1 text-xs text-slate-500">Start typing to search, or select from the list</p>
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Continue to Property
            </button>
            <a href="{{ route('profile.wizard.step-3') }}" class="text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Go Back</a>
        </div>
    </form>
</x-guest-layout>
