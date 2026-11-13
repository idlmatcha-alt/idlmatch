<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberProfileRelatedProfilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_shows_related_profiles_instead_of_placeholder(): void
    {
        $plan = Plan::create([
            'name' => 'Test Paid',
            'price' => 5000,
            'currency' => 'PKR',
            'duration_days' => 30,
            'limits_json' => ['profile_views' => 100],
            'is_active' => true,
        ]);

        $targetOwner = User::factory()->create();
        $targetProfile = Profile::factory()->create([
            'user_id' => $targetOwner->id,
            'display_id' => 'RP9001',
            'gender' => 'female',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'status' => 'active',
        ]);

        $relatedOwnerA = User::factory()->create();
        $relatedProfileA = Profile::factory()->create([
            'user_id' => $relatedOwnerA->id,
            'display_id' => 'RP9002',
            'gender' => 'female',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'status' => 'active',
        ]);

        $relatedOwnerB = User::factory()->create();
        $relatedProfileB = Profile::factory()->create([
            'user_id' => $relatedOwnerB->id,
            'display_id' => 'RP9003',
            'gender' => 'female',
            'city' => 'Karachi',
            'country' => 'Pakistan',
            'status' => 'active',
        ]);

        Subscription::create([
            'user_id' => $relatedOwnerA->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(15),
        ]);

        Subscription::create([
            'user_id' => $relatedOwnerB->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(15),
        ]);

        $response = $this->get(route('profiles.show', $targetProfile->display_id));

        $response->assertOk();
        $response->assertDontSee('Similar profiles will appear here.');
        $response->assertSee(route('profiles.show', $relatedProfileA->display_id), false);
        $response->assertSee(route('profiles.show', $relatedProfileB->display_id), false);
    }
}
