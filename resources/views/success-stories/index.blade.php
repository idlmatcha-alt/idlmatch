@extends('layouts.marketing')


@section('content')
    <section class="section py-10 md:py-16">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-500">Love Stories</p>
                <h1 class="mt-3 text-4xl font-semibold">Success Stories</h1>
            </div>
            <a href="{{ route('success-stories.create') }}" class="btn-primary">Share your story</a>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($stories as $story)
                <a href="{{ route('success-stories.show', $story) }}"
                    class="block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md h-full">
                    <article class="flex flex-col h-full">
                        <div class="relative overflow-hidden bg-slate-100" style="aspect-ratio: 4 / 5; min-height: 15rem;">
                            @if($story->cover_image)
                                <img src="{{ \App\Support\Media::thumb($story->cover_image, 600) }}"
                                    alt="Marriage success story of {{ $story->couple_names }} on Ideal Marriage Center"
                                    class="block"
                                    style="width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                    loading="lazy"
                                    decoding="async"
                                    width="600" height="600">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-rose-50 to-brand-50 text-brand-700">
                                    <span class="text-lg font-semibold">IdealMatch</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-5 flex flex-col grow">
                            <h3 class="font-display text-xl text-slate-900">{{ $story->couple_names }}</h3>
                            <p class="mt-2 text-xs uppercase tracking-wide text-slate-400">
                                {{ $story->created_at->format('M d, Y') }}
                            </p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                {{ \Illuminate\Support\Str::limit($story->story, 180) }}
                            </p>
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-brand-700 pt-3 mt-auto">
                                Read Story
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </article>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500">
                    No approved success stories are available yet.
                </div>
            @endforelse
        </div>
        <div class="mt-8">{{ $stories->withQueryString()->links() }}</div>
    </section>
@endsection
