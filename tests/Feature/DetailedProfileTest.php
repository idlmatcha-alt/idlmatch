<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DetailedProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_1_saves_lifestyle_details()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('profile.wizard.step-1.store'), [
            'gender' => 'male',
            'dob' => '1990-01-01',
            'height_cm' => 175,
            'marital_status' => 'Single',
            'complexion' => 'Fair',
            'body_type' => 'Athletic',
            'eating_habits' => 'Vegetarian',
            'smoking_habits' => 'No',
            'hobbies' => 'Coding, Reading',
        ]);

        $response->assertRedirect(route('profile.wizard.step-2'));

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'complexion' => 'Fair',
            'body_type' => 'Athletic',
            'eating_habits' => 'Vegetarian',
            'smoking_habits' => 'No',
            'hobbies' => 'Coding, Reading',
        ]);
    }

    public function test_step_6_saves_family_values()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('profile.wizard.step-6.store'), [
            'brothers_count' => 1,
            'sisters_count' => 2,
            'married_siblings_count' => 0,
            'family_values' => 'Moderate',
            'family_type' => 'Nuclear Family',
            'family_status' => 'Middle Class',
        ]);

        $response->assertRedirect(route('profile.wizard.step-7'));

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'family_values' => 'Moderate',
            'family_type' => 'Nuclear Family',
            'family_status' => 'Middle Class',
        ]);
    }

    public function test_step_8_saves_detailed_preferences()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('profile.wizard.step-8.store'), [
            'desired_complexion' => 'Fair',
            'desired_body_type' => 'Slim',
            'desired_family_values' => 'Moderate',
        ]);

        $response->assertRedirect(route('packages.index'));

        $this->assertDatabaseHas('preferences', [
            'profile_id' => $profile->id,
            'desired_complexion' => 'Fair',
            'desired_body_type' => 'Slim',
            'desired_family_values' => 'Moderate',
        ]);
    }
}
