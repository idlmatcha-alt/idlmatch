@extends('layouts.marketing')


@section('content')
    <section class="bg-gradient-to-r from-[#fff5f8] via-white to-[#f4f9ff] border-b border-slate-200">
        <div class="section py-14">
            <p class="text-xs font-extrabold uppercase tracking-[0.25em] text-brand-600">Marketing Blog</p>
            <h1 class="mt-3 text-3xl md:text-5xl font-extrabold text-slate-900 leading-tight">
                Marriage Advice, Success Tips, and Relationship Insights
            </h1>
            <p class="mt-4 max-w-3xl text-sm md:text-base text-slate-600">
                Explore expert guidance from Ideal Marriage Center. Each article is written to help families and members
                make better matchmaking decisions. This page rotates published articles from our backend.
            </p>
        </div>
    </section>

    <section class="section py-10">
        <div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            Showing a random mix of published blog posts.
        </div>
        <div class="grid lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8">
                @if($posts->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500">
                        No published blog articles are available yet.
                    </div>
                @else
                    <div class="grid sm:grid-cols-2 gap-6">
                        @foreach($posts as $post)
                            @php
                                $postImage = $post->featured_image ?: $post->cover_image;
                                $postExcerpt = trim((string) $post->excerpt);
                            @endphp
                            <article class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-lg transition flex flex-col h-full">
                                <a href="{{ route('blog.show', $post) }}" class="block bg-slate-100 overflow-hidden" style="aspect-ratio: 16 / 10; min-height: 12rem;">
                                    @if($postImage)
                                        <img src="{{ \App\Support\Media::thumb($postImage, 640) }}"
                                            alt="{{ $post->title }} — marriage advice article by Ideal Marriage Center"
                                            class="block"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            loading="lazy"
                                            decoding="async"
                                            width="640" height="400">
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 text-sm font-semibold">
                                            Blog Article
                                        </div>
                                    @endif
                                </a>
                                <div class="p-5 flex flex-col grow">
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">
                                        {{ optional($post->published_at)->format('M d, Y') ?? $post->created_at->format('M d, Y') }}
                                    </p>
                                    <h2 class="mt-2 text-xl font-bold text-slate-900 leading-snug line-clamp-2">
                                        <a href="{{ route('blog.show', $post) }}" class="hover:text-brand-700 transition">
                                            {{ $post->title }}
                                        </a>
                                    </h2>
                                    <p class="mt-2 text-sm text-slate-600 line-clamp-3">
                                        {{ $postExcerpt !== '' ? $postExcerpt : \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 130) }}
                                    </p>
                                    <a href="{{ route('blog.show', $post) }}"
                                        class="inline-flex items-center gap-1 text-sm font-bold text-brand-700 hover:text-brand-800 mt-auto pt-4">
                                        Read Article
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

            <div class="lg:col-span-4 space-y-5">
                @include('blog.partials.sidebar-profiles', [
                    'profiles' => $sidebarProfiles,
                    'countryHint' => $countryHint,
                ])

                <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h3 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Quick Blog Links</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach($posts->getCollection()->take(5) as $post)
                            <li>
                                <a href="{{ route('blog.show', $post) }}"
                                    class="text-sm text-slate-700 hover:text-brand-700 hover:underline line-clamp-1">
                                    {{ $post->title }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ route('blog.index') }}" class="text-sm font-bold text-brand-700 hover:text-brand-800">
                                Refresh random posts
                            </a>
                        </li>
                    </ul>
                </aside>
            </div>
        </div>
    </section>
@endsection
