@extends('layouts.marketing')


@section('content')
    <section class="pt-8 pb-12 min-h-screen" style="background-color: #f8f7fc;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            {{-- Hero Section --}}
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold mb-3" style="color: #E91E63;">💎 Elite Matrimonial</h1>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Premium profiles of distinguished members with {{ $planName }}. 
                    Connect with verified, upper-class individuals seeking meaningful relationships.
                </p>
                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">Elite Members</span>
                </div>
            </div>

            {{-- Profile Cards --}}
            <div class="space-y-4">
                @forelse($profiles as $profile)
                    <x-profile-card :profile="$profile" />
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <h3 class="mt-4 text-xl font-bold text-slate-900">No Elite Members Available</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            Currently, there are no members with {{ $planName }}. Check back soon for exclusive elite profiles!
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('profiles.index') }}" 
                                class="inline-block px-6 py-3 rounded-lg text-sm font-bold text-white transition-colors"
                                style="background: linear-gradient(135deg, #8B5CF6, #6366F1);">
                                Browse All Profiles
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($profiles->hasPages())
                <div class="mt-8">
                    {{ $profiles->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
