<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationEnhancementTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_with_enhanced_details()
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'profile_for' => 'Myself',
            'gender' => 'male',
            'dob' => '1990-01-01',
            'mobile_no' => '+923001234567',
            'country' => 'Pakistan',
            'state' => 'Punjab',
            'city' => 'Lahore',
            'marital_status' => 'Single',
            'height_cm' => 175,
            
            // New Fields
            'religion' => 'Islam',
            'sect' => 'Sunni',
            'caste' => 'Arain',
            'mother_tongue' => 'Urdu',
            'father_name' => 'Test Father',
            'father_occupation' => 'Businessman',
            'mother_occupation' => 'Housewife',
            'brothers_count' => 2,
            'sisters_count' => 1,
            'married_siblings_count' => 0,
            'disability' => 'None',
            
            // Required for Profile creation but might be nullable or default
            'education' => 'Bachelors',
            'major_course' => 'Computer Science',
            'job_title' => 'Software Engineer',
            'income' => '50k - 100k',
            'home_status' => 'Own',
            'home_size' => '10 Marla',
            'profile_visibility' => 'public',
            'declaration_agreement' => 'agree',
        ]);

        $this->assertAuthenticated();
        
        $response->assertRedirect(route('profile.verify'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        // Verify Profile Data
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'mother_tongue' => 'Urdu',
            'father_name' => 'Test Father',
            'father_occupation' => 'Businessman',
            'brothers_count' => 2,
            'disability' => 'None',
            'caste' => 'Arain',
        ]);
    }

    public function test_registration_is_blocked_when_user_disagrees_declaration()
    {
        $response = $this->from('/register')->post('/register', [
            'first_name' => 'No',
            'last_name' => 'Agree',
            'email' => 'noagree@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'profile_for' => 'Myself',
            'gender' => 'female',
            'dob' => '1993-05-02',
            'mobile_no' => '+923001234568',
            'country' => 'Pakistan',
            'state' => 'Punjab',
            'city' => 'Lahore',
            'marital_status' => 'Single',
            'height_cm' => 165,
            'religion' => 'Islam',
            'sect' => 'Sunni',
            'caste' => 'Arain',
            'mother_tongue' => 'Urdu',
            'education' => 'Bachelors',
            'major_course' => 'Computer Science',
            'job_title' => 'Designer',
            'income' => '50k - 100k',
            'home_status' => 'Own',
            'home_size' => '10 Marla',
            'profile_visibility' => 'public',
            'declaration_agreement' => 'disagree',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('declaration_agreement');
        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'noagree@example.com',
        ]);
    }
}
