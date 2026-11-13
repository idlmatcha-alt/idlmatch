@extends('layouts.marketing')


@section('content')
    <section class="section py-10 md:py-14">
        <div class="max-w-4xl mx-auto">
            <nav class="text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-brand-700">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('success-stories.index') }}" class="hover:text-brand-700">Success Stories</a>
                <span class="mx-2">/</span>
                <span class="text-slate-700">{{ $successStory->couple_names }}</span>
            </nav>

            <article class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="relative overflow-hidden bg-slate-100" style="aspect-ratio: 16 / 9; min-height: 16rem;">
                    @if($successStory->cover_image)
                        <img src="{{ \App\Support\Media::full($successStory->cover_image) }}"
                            alt="{{ $successStory->couple_names }}"
                            class="block"
                            style="width: 100%; height: 100%; object-fit: contain; object-position: center;"
                            loading="lazy"
                            decoding="async">
                    @else
                        <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-rose-50 to-brand-50 text-brand-700">
                            <span class="text-xl font-semibold">IdealMatch</span>
                        </div>
                    @endif
                </div>

                <div class="p-6 md:p-8">
                    <p class="text-xs uppercase tracking-widest text-slate-400">
                        {{ $successStory->created_at->format('F d, Y') }}
                    </p>
                    <h1 class="mt-2 text-3xl md:text-4xl font-display font-semibold text-slate-900">
                        {{ $successStory->couple_names }}
                    </h1>
                    <p class="mt-5 text-base leading-8 text-slate-700 whitespace-pre-line">
                        {{ $successStory->story }}
                    </p>

                    <div class="mt-8">
                        <a href="{{ route('success-stories.index') }}"
                            class="inline-flex items-center gap-2 text-sm font-bold text-brand-700 hover:text-brand-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to all success stories
                        </a>
                    </div>
                </div>
            </article>

            @if($relatedStories->isNotEmpty())
                <div class="mt-10">
                    <h2 class="text-2xl font-display font-semibold text-slate-900">More Stories</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($relatedStories as $story)
                            <a href="{{ route('success-stories.show', $story) }}"
                                class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition">
                                <p class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $story->couple_names }}</p>
                                <p class="mt-2 text-xs uppercase tracking-wide text-slate-400">
                                    {{ $story->created_at->format('M d, Y') }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
