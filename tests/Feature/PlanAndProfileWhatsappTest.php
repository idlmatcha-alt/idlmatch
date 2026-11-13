<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Profile;
use App\Models\ProfileView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanAndProfileWhatsappTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_duration_days_can_be_updated(): void
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
            'price' => 5000,
            'currency' => 'PKR',
            'duration_days' => 30,
            'limits_json' => ['profile_views' => 10, 'contact_access' => true, 'verified_badge' => false],
            'is_active' => true,
        ]);

        $plan->update(['duration_days' => 75]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'duration_days' => 75,
        ]);
    }

    public function test_non_owner_sees_profile_whatsapp_card_with_profile_link(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $owner->id,
            'display_id' => 'WA9999',
            'status' => 'active',
        ]);

        $response = $this->get(route('profiles.show', $profile->display_id));

        $response->assertOk();
        $response->assertSee("If you are interested in this profile, let's chat", false);
        $response->assertSee('https://api.whatsapp.com/send/?phone=923122294445', false);
        $response->assertSee(urlencode('Assalam o Alaikum. Guest client is interested in this profile.'), false);
        $response->assertSee(urlencode('Interested profile link: '), false);
        $response->assertSee(urlencode(route('profiles.show', $profile->display_id)), false);
    }

    public function test_owner_does_not_see_profile_whatsapp_card(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $owner->id,
            'display_id' => 'WA0001',
            'status' => 'active',
        ]);

        $response = $this->actingAs($owner)->get(route('profiles.show', $profile->display_id));

        $response->assertOk();
        $response->assertDontSee("If you are interested in this profile, let's chat");
        $response->assertDontSee("Let's Chat");
    }

    public function test_logged_in_member_whatsapp_message_contains_both_profile_links(): void
    {
        $owner = User::factory()->create();
        $targetProfile = Profile::factory()->create([
            'user_id' => $owner->id,
            'display_id' => 'WA2222',
            'status' => 'active',
        ]);

        $viewer = User::factory()->create();
        $viewerProfile = Profile::factory()->create([
            'user_id' => $viewer->id,
            'display_id' => 'WA3333',
            'status' => 'active',
        ]);

        ProfileView::create([
            'viewer_id' => $viewer->id,
            'viewed_id' => $targetProfile->id,
        ]);

        $response = $this->actingAs($viewer)->get(route('profiles.show', $targetProfile->display_id));

        $response->assertOk();
        $response->assertSee(urlencode('Assalam o Alaikum. I am interested in this profile.'), false);
        $response->assertSee(urlencode('My profile link: '), false);
        $response->assertSee(urlencode(route('profiles.show', $targetProfile->display_id)), false);
        $response->assertSee(urlencode(route('profiles.show', $viewerProfile->display_id)), false);
    }
}
