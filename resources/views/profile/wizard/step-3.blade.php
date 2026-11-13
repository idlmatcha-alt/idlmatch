<x-guest-layout title="Step 3: Verification">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-cta">Step 3 of 3</span>
            <span class="text-xs font-bold text-slate-400">Final Step</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-cta transition-all duration-500" style="width: 100%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Identity Verification</h1>
        <p class="text-slate-500 mt-1">Please upload your ID Card and a Selfie for verification. These details are only visible to the Admin.</p>
    </div>

    <form method="POST" action="{{ route('profile.wizard.step-3.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- CNIC Front -->
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">*CNIC Front Side</label>
            <div class="relative group">
                <input type="file" name="cnic_front" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cta/10 file:text-cta hover:file:bg-cta/20 focus:outline-none transition-all cursor-pointer" required>
            </div>
        </div>

        <!-- CNIC Back -->
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">*CNIC Back Side</label>
            <div class="relative group">
                <input type="file" name="cnic_back" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cta/10 file:text-cta hover:file:bg-cta/20 focus:outline-none transition-all cursor-pointer" required>
            </div>
        </div>

        <!-- Selfie -->
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">*Selfie</label>
            <p class="text-xs text-slate-500 mb-2">Please take a clear photo of your face.</p>
            <div class="relative group">
                <input type="file" name="selfie" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cta/10 file:text-cta hover:file:bg-cta/20 focus:outline-none transition-all cursor-pointer" required>
            </div>
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Submit Verification
            </button>
            <a href="{{ route('profile.wizard.step-2') }}" class="text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Go Back</a>
        </div>
    </form>
</x-guest-layout>
