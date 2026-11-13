@extends('layouts.marketing')


@section('content')
    <section class="section max-w-3xl py-16">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-500">Share</p>
        <h1 class="mt-3 text-4xl font-semibold">Share your story</h1>
        <p class="mt-4 text-slate-600">Tell the community how you met and what made your journey special.</p>

        <form method="POST" action="{{ route('success-stories.store') }}" enctype="multipart/form-data"
            class="mt-8 grid gap-4 rounded-2xl border border-slate-100 bg-white p-6">
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
            <div>
                <label class="text-sm font-medium text-slate-600">Couple Names</label>
                <input type="text" name="couple_names" class="mt-2 w-full field" required>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Story</label>
                <textarea name="story" rows="6" class="mt-2 w-full field" required></textarea>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Cover Image (optional)</label>
                <input type="file" name="cover_image" class="mt-2 w-full field">
            </div>
            <button class="btn-primary w-fit">Submit for review</button>
        </form>
    </section>
@endsection