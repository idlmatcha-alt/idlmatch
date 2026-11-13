@extends('layouts.marketing')




@section('content')
    <section class="section py-10">
        <nav class="mb-6">
            <ol class="flex items-center gap-2 text-sm text-slate-500">
                <li><a href="{{ route('home') }}" class="hover:text-brand-600">Home</a></li>
                <li>/</li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a></li>
                <li>/</li>
                <li class="text-slate-900 font-medium truncate max-w-xs">{{ $blogPost->title }}</li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-12 gap-8 items-start">
            <article class="lg:col-span-8">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
                    <header>
                        <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-brand-600">Article</p>
                        <h1 class="mt-3 text-3xl md:text-4xl font-bold text-slate-900">{{ $blogPost->title }}</h1>
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                            <time datetime="{{ $blogPost->published_at?->toISOString() }}">
                                Published {{ optional($blogPost->published_at)->format('F d, Y') ?? $blogPost->created_at->format('F d, Y') }}
                            </time>
                            @if($blogPost->author)
                                <span>by {{ $blogPost->author->name }}</span>
                            @endif
                            @if($countryHint)
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-xs font-bold text-brand-700">
                                    Focus: {{ $countryHint }}
                                </span>
                            @endif
                        </div>
                    </header>

                    @if($blogPost->featured_image || $blogPost->cover_image)
                        <div class="mt-6 rounded-2xl overflow-hidden border border-slate-200">
                            <img src="{{ \App\Support\Media::thumb($blogPost->featured_image ?: $blogPost->cover_image, 1200) }}"
                                alt="{{ $blogPost->title }}"
                                class="w-full h-auto max-h-[520px] object-cover"
                                loading="lazy"
                                decoding="async">
                        </div>
                    @endif

                    <div class="prose prose-slate max-w-none mt-8 prose-headings:font-bold prose-a:text-brand-700 prose-a:no-underline hover:prose-a:underline">
                        {!! $blogPost->content !!}
                    </div>

                    <div class="mt-10 pt-6 border-t border-slate-200">
                        <p class="text-sm font-semibold text-slate-600 mb-3">Share this article:</p>
                        <div class="flex flex-wrap gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center rounded-lg bg-blue-600 text-white px-3 py-2 text-sm font-semibold hover:bg-blue-700 transition">
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blogPost->title) }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center rounded-lg bg-black text-white px-3 py-2 text-sm font-semibold hover:bg-slate-800 transition">
                                X
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($blogPost->title . ' ' . url()->current()) }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center rounded-lg bg-green-600 text-white px-3 py-2 text-sm font-semibold hover:bg-green-700 transition">
                                WhatsApp
                            </a>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('blog.index') }}"
                            class="inline-flex items-center text-brand-700 font-semibold hover:text-brand-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            Back to Blog
                        </a>
                    </div>
                </div>
            </article>

            <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-28">
                @include('blog.partials.sidebar-profiles', [
                    'profiles' => $sidebarProfiles,
                    'countryHint' => $countryHint,
                ])

                <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h3 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Latest Blog Posts</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($latestPosts as $post)
                            <a href="{{ route('blog.show', $post) }}"
                                class="block rounded-lg border border-slate-200 p-3 hover:border-brand-300 hover:bg-brand-50/20 transition">
                                <p class="text-sm font-semibold text-slate-900 line-clamp-2">{{ $post->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ optional($post->published_at)->format('M d, Y') ?? $post->created_at->format('M d, Y') }}
                                </p>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">No additional posts yet.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

