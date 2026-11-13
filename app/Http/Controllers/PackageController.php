<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PackageController extends Controller
{
    public function index()
    {
        $this->ensureDefaultPlans();

        $plansQuery = Plan::query();
        if (Schema::hasColumn('plans', 'is_active')) {
            $plansQuery->where('is_active', true);
        } elseif (Schema::hasColumn('plans', 'status')) {
            $plansQuery->where('status', 'active');
        }

        $plans = $plansQuery->orderBy('price')->get();
        $currentPlanId = auth()->check() ? auth()->user()->getActiveSubscription()?->plan_id : null;

        // Check for pending subscription
        $pendingSubscription = auth()->check()
            ? auth()->user()->subscriptions()->where('status', 'pending')->first()
            : null;

        return view('packages.index', compact('plans', 'currentPlanId', 'pendingSubscription'));
    }

    private function ensureDefaultPlans(): void
    {
        if (Plan::query()->exists()) {
            return;
        }

        $defaults = [
            [
                'name' => 'Basic',
                'price' => 0,
                'currency' => 'PKR',
                'duration_days' => 30,
                'limits_json' => ['profile_views' => 10, 'contact_access' => false, 'verified_badge' => false],
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'price' => 2500,
                'currency' => 'PKR',
                'duration_days' => 90,
                'limits_json' => ['profile_views' => 30, 'contact_access' => true, 'verified_badge' => false],
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'price' => 6500,
                'currency' => 'PKR',
                'duration_days' => 180,
                'limits_json' => ['profile_views' => 100, 'contact_access' => true, 'verified_badge' => true],
                'is_active' => true,
            ],
            [
                'name' => 'Upper Class',
                'price' => 12000,
                'currency' => 'PKR',
                'duration_days' => 365,
                'limits_json' => ['profile_views' => 500, 'contact_access' => true, 'verified_badge' => true],
                'is_active' => true,
            ],
        ];

        $hasIsActive = Schema::hasColumn('plans', 'is_active');
        $hasStatus = Schema::hasColumn('plans', 'status');

        foreach ($defaults as $planData) {
            if (!$hasIsActive) {
                unset($planData['is_active']);
            }
            if ($hasStatus) {
                $planData['status'] = 'active';
            }

            Plan::updateOrCreate(['name' => $planData['name']], $planData);
        }
    }

    public function select(Plan $plan)
    {
        // Block if pending
        if (auth()->user()->subscriptions()->where('status', 'pending')->exists()) {
            return redirect()->route('packages.index')->with('error', 'You have a pending subscription verification. Please wait for approval.');
        }

        // If free plan, auto-activate subscription and redirect to home
        if ($plan->price == 0) {
            $user = auth()->user();

            // Ensure a single active subscription state for this user.
            \App\Models\Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'cancelled',
                    'end_at' => now(),
                ]);

            // Create free subscription immediately
            \App\Models\Subscription::updateOrCreate([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ], [
                'status' => 'active',
                'start_at' => now(),
                'end_at' => now()->addDays($plan->duration_days ?? 30),
                'payment_method' => 'free',
            ]);

            return redirect()->route('profiles.index')->with('status', 'Your free plan has been activated!');
        }

        return view('packages.payment-instructions', compact('plan'));
    }
}
