@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="font-display text-3xl">Your Shortlist</h1>
        <div class="mt-8 space-y-4">
            @forelse($favorites as $favorite)
                @if($favorite->targetProfile)
                    <div>
                        <x-profile-card :profile="$favorite->targetProfile" />
                        <div class="mt-2 flex justify-end">
                            <form method="POST" action="{{ route('favorites.destroy', $favorite->targetProfile->display_id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @empty
                <div class="card p-6 text-sm text-slate-500">No favorites yet.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $favorites->links() }}</div>
    </div>
@endsection
