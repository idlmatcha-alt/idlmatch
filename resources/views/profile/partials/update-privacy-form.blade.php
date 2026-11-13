<section>
    <header>
        <h2 class="text-lg font-semibold text-slate-900">
            Privacy Settings
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            Control who can see your contact details.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label class="text-sm font-semibold text-slate-700">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->profile->phone ?? '') }}" class="mt-2 w-full field">
        </div>

        <div class="flex items-center gap-3">
            <input type="hidden" name="hide_contact" value="0">
            <input id="hide_contact" name="hide_contact" type="checkbox" value="1" class="rounded border-slate-300" @checked(old('hide_contact', data_get($user->profile, 'privacy_json.hide_contact')))>
            <label for="hide_contact" class="text-sm text-slate-600">Hide contact details until mutual interest is accepted.</label>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
