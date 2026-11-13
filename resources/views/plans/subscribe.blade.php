@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="font-display text-3xl">Subscribe to {{ $plan->name }}</h1>
        <p class="mt-2 text-slate-600">Upload your payment proof. Our team will verify it within 24 hours.</p>

        <form method="POST" action="{{ route('plans.subscribe.store', $plan) }}" enctype="multipart/form-data" class="mt-8 grid gap-4">
            @csrf
            <input type="hidden" name="payment_method" value="manual">
            <div class="card p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-800">Bank transfer / Easypaisa / JazzCash</p>
                <p>Account details will be shared by admin in production.</p>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Payment screenshot</label>
                <input type="file" name="payment_proof" class="mt-2 w-full field">
            </div>
            <button class="btn-primary w-fit">Submit payment proof</button>
        </form>
    </div>
@endsection
