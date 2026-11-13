@extends('layouts.marketing')


@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-16">
            <h5 class="text-brand-600 font-bold tracking-widest uppercase text-sm mb-3">Technology Partner</h5>
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6 font-display">IDLTech</h1>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                IDLTech builds and maintains the digital products behind Ideal Marriage Center — from the website and
                member portal to the tools our team uses every day.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-brand-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Web Development</h3>
                <p class="text-slate-600">Secure, responsive web applications built to perform reliably at scale.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-brand-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Product &amp; Design</h3>
                <p class="text-slate-600">Clear interfaces and thoughtful user journeys that make complex tasks simple.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-brand-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Support &amp; Maintenance</h3>
                <p class="text-slate-600">Ongoing monitoring, updates, and technical support to keep everything running smoothly.</p>
            </div>
        </div>

        <div class="bg-slate-50 rounded-3xl p-8 md:p-12">
            <div class="grid md:grid-cols-3 gap-8 text-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Privacy First</h3>
                    <p class="text-slate-600">Member data is handled with strict security practices and controlled access.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Built for Reliability</h3>
                    <p class="text-slate-600">Performance, uptime, and stability are treated as core product requirements.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Long-Term Partnership</h3>
                    <p class="text-slate-600">We stay involved after launch with continuous improvement and support.</p>
                </div>
            </div>
        </div>

        <div class="mt-16 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-4">Need help with the platform?</h2>
            <p class="text-slate-600 mb-8">Our support team can help with account questions, technical issues, and feedback.</p>
            <a href="{{ route('contact') }}"
                class="inline-flex items-center px-6 py-3 bg-brand-600 text-white rounded-lg font-bold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
                Contact Support
            </a>
        </div>
    </section>
@endsection
