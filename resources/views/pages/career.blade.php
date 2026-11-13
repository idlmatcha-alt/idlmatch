@extends('layouts.marketing')


@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-16">
            <h5 class="text-brand-600 font-bold tracking-widest uppercase text-sm mb-3">Join Our Team</h5>
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6 font-display">Build the Future of Matchmaking</h1>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                At Ideal Marriage Center, we are driven by a single mission: to help people find their perfect life partner. 
                Join us in creating meaningful connections.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-brand-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Impact</h3>
                <p class="text-slate-600">Work on products that touch millions of lives and help create happy families.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-brand-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Growth</h3>
                <p class="text-slate-600">Fast-paced environment with opportunities to learn, lead, and grow your career.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-brand-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Culture</h3>
                <p class="text-slate-600">A collaborative, inclusive, and fun workplace where every voice matters.</p>
            </div>
        </div>

        <div class="bg-slate-50 rounded-3xl p-8 md:p-12 text-center">
            <h2 class="text-3xl font-bold text-slate-900 mb-6">Current Openings</h2>
            
            <div class="max-w-2xl mx-auto py-12">
                <div class="mb-8 flex justify-center">
                    <div class="w-24 h-24 bg-slate-200 rounded-full flex items-center justify-center text-slate-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No Openings at the Moment</h3>
                <p class="text-slate-600 mb-8">
                    We are not currently hiring for specific roles, but we are always looking for talented individuals to join our mission.
                </p>
                
                <div class="bg-white p-6 rounded-xl border border-dashed border-slate-300">
                    <h4 class="font-bold text-lg text-slate-900 mb-2">Join our Talent Pool</h4>
                    <p class="text-slate-500 text-sm mb-4">Send your CV and we'll contact you when a matching role opens up.</p>
                    <a href="mailto:careers@idlmatch.com" class="inline-flex items-center px-6 py-3 bg-brand-600 text-white rounded-lg font-bold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Email Your Resume
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
