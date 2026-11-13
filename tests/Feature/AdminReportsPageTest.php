<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_reports_index_page_loads_successfully(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $reporterUser = User::factory()->create();
        $reporterProfile = Profile::factory()->create([
            'user_id' => $reporterUser->id,
            'display_id' => 'AR1001',
            'status' => 'active',
        ]);

        $targetUser = User::factory()->create();
        $targetProfile = Profile::factory()->create([
            'user_id' => $targetUser->id,
            'display_id' => 'AR1002',
            'status' => 'active',
        ]);

        Report::create([
            'reporter_profile_id' => $reporterProfile->id,
            'target_profile_id' => $targetProfile->id,
            'category' => 'Fake Profile',
            'details' => 'This profile appears fake and has misleading content.',
            'status' => 'open',
        ]);

        $response = $this->actingAs($admin)->get('/admin/reports');

        $response->assertOk();
        $response->assertSee('Reported Profiles');
        $response->assertSee('Reason');
        $response->assertSee('View Reason');
        $response->assertSee('This profile appears fake and has misleading content.');
    }
}
