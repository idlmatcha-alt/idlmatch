<x-guest-layout title="Profile Complete">
    <div class="text-center py-4">
        <div class="mb-6 flex justify-center">
            <div class="h-20 w-20 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <h1 class="text-3xl font-bold text-slate-900 leading-tight">Profile Saved!</h1>
        <p class="mt-4 text-slate-500 font-medium px-4">
            Your profile has been updated successfully.
        </p>

        <div class="mt-10 space-y-4 px-4">
            <a href="{{ route('dashboard') }}" 
               class="block w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Go to Dashboard
            </a>
            <p class="text-xs text-slate-400 font-medium">You can continue editing and browsing profiles anytime.</p>
        </div>
    </div>
</x-guest-layout>
