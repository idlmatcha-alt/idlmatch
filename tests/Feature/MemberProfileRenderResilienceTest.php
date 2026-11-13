<?php

namespace Tests\Feature;

use App\Models\Preference;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberProfileRenderResilienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_renders_when_preferences_contain_array_values(): void
    {
        $owner = User::factory()->create();

        $profile = Profile::factory()->create([
            'user_id' => $owner->id,
            'display_id' => 'RS5001',
            'status' => 'active',
        ]);

        Preference::create([
            'profile_id' => $profile->id,
            'desired_country' => 'Pakistan',
            'desired_city' => 'Lahore',
            'desired_education' => [],
            'desired_professions' => ['Engineer', 'Teacher'],
            'desired_sects' => [],
            'desired_marital_statuses' => [],
            'desired_countries' => [],
            'desired_cities' => [],
        ]);

        $response = $this->get(route('profiles.show', $profile->display_id));

        $response->assertOk();
        $response->assertSee('Partner Preferences');
        $response->assertSee('Engineer, Teacher');
    }
}
