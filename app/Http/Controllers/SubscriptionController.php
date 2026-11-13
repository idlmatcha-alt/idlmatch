<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\ImageService;
use App\Support\Tracking;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function create(Plan $plan)
    {
        return view('plans.subscribe', compact('plan'));
    }

    public function store(SubscriptionRequest $request, Plan $plan, ImageService $imageService)
    {
        $paymentMethod = $request->input('payment_method', 'manual');
        $user = $request->user();

        if ($user->subscriptions()->where('status', 'pending')->exists()) {
            return redirect()->route('packages.index')
                ->withErrors(['payment' => 'You already have a pending payment request. Please wait for admin review.']);
        }

        if ($paymentMethod === 'stripe' && config('services.stripe.enabled')) {
            return redirect()->back()->withErrors(['payment' => 'Stripe is enabled but not configured in this build.']);
        }

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
        ]);

        if (in_array($paymentMethod, ['manual', 'bank_transfer', 'jazzcash', 'easypaisa']) && $request->hasFile('payment_proof')) {
            $subscription->payment_proof_path = $imageService->optimize($request->file('payment_proof'), 'payment-proofs');
            $subscription->save();
        }

        Tracking::meta('Lead', ['content_name' => 'payment_submitted']);

        return redirect()->route('dashboard')->with('status', 'Payment submitted successfully! Your subscription is pending verification.');
    }
}
