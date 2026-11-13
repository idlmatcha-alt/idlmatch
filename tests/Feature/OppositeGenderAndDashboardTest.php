<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OppositeGenderAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_profiles_index_defaults_to_opposite_gender_for_logged_in_user(): void
    {
        $plan = Plan::create([
            'name' => 'Premium',
            'price' => 3000,
            'currency' => 'PKR',
            'duration_days' => 30,
            'limits_json' => ['profile_views' => 100],
            'is_active' => true,
        ]);

        $viewer = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'Hamza Member',
        ]);
        $viewerProfile = Profile::factory()->create([
            'user_id' => $viewer->id,
            'display_id' => 'VW1001',
            'gender' => 'male',
            'status' => 'active',
        ]);

        Subscription::create([
            'user_id' => $viewer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(15),
        ]);

        $femaleUser = User::factory()->create(['name' => 'Ayesha Candidate']);
        $femaleProfile = Profile::factory()->create([
            'user_id' => $femaleUser->id,
            'display_id' => 'FM1001',
            'gender' => 'female',
            'status' => 'active',
        ]);

        $maleUser = User::factory()->create(['name' => 'Bilal Candidate']);
        $maleProfile = Profile::factory()->create([
            'user_id' => $maleUser->id,
            'display_id' => 'ML1001',
            'gender' => 'male',
            'status' => 'active',
        ]);

        Subscription::create([
            'user_id' => $femaleUser->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(15),
        ]);

        Subscription::create([
            'user_id' => $maleUser->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(15),
        ]);

        $response = $this->actingAs($viewer)->get(route('profiles.index'));

        $response->assertOk();
        $response->assertSee(route('profiles.show', $femaleProfile->display_id), false);
        $response->assertDontSee(route('profiles.show', $maleProfile->display_id), false);
        $response->assertDontSee(route('profiles.show', $viewerProfile->display_id), false);
    }

    public function test_related_profiles_follow_viewer_opposite_gender_preference(): void
    {
        $plan = Plan::create([
            'name' => 'Viewer Plan',
            'price' => 1000,
            'currency' => 'PKR',
            'duration_days' => 30,
            'limits_json' => ['profile_views' => 100],
            'is_active' => true,
        ]);

        $viewer = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'Raza Male',
        ]);
        $viewerProfile = Profile::factory()->create([
            'user_id' => $viewer->id,
            'display_id' => 'VM1001',
            'gender' => 'male',
            'status' => 'active',
        ]);

        Subscription::create([
            'user_id' => $viewer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(20),
        ]);

        $targetOwner = User::factory()->create(['name' => 'Target Female']);
        $targetProfile = Profile::factory()->create([
            'user_id' => $targetOwner->id,
            'display_id' => 'TG1001',
            'gender' => 'female',
            'status' => 'active',
            'country' => 'Pakistan',
            'city' => 'Lahore',
        ]);

        $femaleRelatedA = Profile::factory()->create([
            'display_id' => 'RF1001',
            'gender' => 'female',
            'status' => 'active',
            'country' => 'Pakistan',
            'city' => 'Lahore',
        ]);
        $femaleRelatedB = Profile::factory()->create([
            'display_id' => 'RF1002',
            'gender' => 'female',
            'status' => 'active',
            'country' => 'Pakistan',
            'city' => 'Lahore',
        ]);
        $femaleRelatedC = Profile::factory()->create([
            'display_id' => 'RF1003',
            'gender' => 'female',
            'status' => 'active',
            'country' => 'Pakistan',
            'city' => 'Lahore',
        ]);
        $femaleRelatedD = Profile::factory()->create([
            'display_id' => 'RF1004',
            'gender' => 'female',
            'status' => 'active',
            'country' => 'Pakistan',
            'city' => 'Lahore',
        ]);
        $maleRelated = Profile::factory()->create([
            'display_id' => 'RM1001',
            'gender' => 'male',
            'status' => 'active',
            'country' => 'Pakistan',
            'city' => 'Lahore',
        ]);

        $response = $this->actingAs($viewer)->get(route('profiles.show', $targetProfile->display_id));

        $response->assertOk();
        $response->assertSee(route('profiles.show', $femaleRelatedA->display_id), false);
        $response->assertSee(route('profiles.show', $femaleRelatedB->display_id), false);
        $response->assertSee(route('profiles.show', $femaleRelatedC->display_id), false);
        $response->assertSee(route('profiles.show', $femaleRelatedD->display_id), false);
        $response->assertDontSee(route('profiles.show', $maleRelated->display_id), false);
        $response->assertDontSee(route('profiles.show', $viewerProfile->display_id), false);
    }

    public function test_dashboard_shows_time_greeting_and_rotating_quote_banner(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'Amina User',
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_id' => 'DB1001',
            'gender' => 'female',
            'status' => 'active',
        ]);

        $firstResponse = $this->actingAs($user)->get(route('dashboard'));
        $firstResponse->assertOk();
        $firstResponse->assertSee('Welcome');
        $firstResponse->assertSee('Daily Message');

        $firstIndex = session('dashboard_last_quote_index');
        $this->assertNotNull($firstIndex);

        $secondResponse = $this->actingAs($user)->get(route('dashboard'));
        $secondResponse->assertOk();

        $secondIndex = session('dashboard_last_quote_index');
        $this->assertNotNull($secondIndex);
        $this->assertNotSame($firstIndex, $secondIndex);
    }
}

