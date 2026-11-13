<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPhotosPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_photos_index_loads_and_shows_profile_details(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $member = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $member->id,
            'display_id' => 'PH7001',
            'status' => 'active',
        ]);

        Photo::create([
            'profile_id' => $profile->id,
            'path' => 'photos/main.jpg',
            'status' => 'approved',
            'is_primary' => true,
        ]);

        Photo::create([
            'profile_id' => $profile->id,
            'path' => 'photos/gallery-1.jpg',
            'status' => 'approved',
            'is_primary' => false,
        ]);

        $response = $this->actingAs($admin)->get('/admin/photos');

        $response->assertOk();
        $response->assertSee('PH7001');
        $response->assertSee('Gallery Photos');
    }
}
