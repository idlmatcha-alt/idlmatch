<?php

namespace Tests\Feature;

use App\Mail\ReportBanThankYou;
use App\Mail\ReportBannedNotice;
use App\Models\Profile;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReportModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_requires_reason_text(): void
    {
        $reporter = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $reporter->id,
            'display_id' => 'RP1111',
            'status' => 'active',
        ]);

        $targetOwner = User::factory()->create();
        $targetProfile = Profile::factory()->create([
            'user_id' => $targetOwner->id,
            'display_id' => 'RP2222',
            'status' => 'active',
        ]);

        $response = $this->actingAs($reporter)
            ->post(route('reports.store', $targetProfile->display_id), [
                'category' => 'Fake Profile',
                'details' => '',
            ]);

        $response->assertSessionHasErrors('details');
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_admin_can_ban_reported_user_and_emails_are_sent(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $reporterUser = User::factory()->create(['status' => 'active']);
        $reporterProfile = Profile::factory()->create([
            'user_id' => $reporterUser->id,
            'display_id' => 'RP3001',
            'status' => 'active',
        ]);

        $targetUser = User::factory()->create(['status' => 'active']);
        $targetProfile = Profile::factory()->create([
            'user_id' => $targetUser->id,
            'display_id' => 'RP3002',
            'status' => 'active',
        ]);

        $report = Report::create([
            'reporter_profile_id' => $reporterProfile->id,
            'target_profile_id' => $targetProfile->id,
            'category' => 'Harassment / Abusive Behavior',
            'details' => 'User is repeatedly sending abusive language.',
            'status' => 'open',
        ]);

        app(ReportModerationService::class)->banTarget($report, $admin);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'banned',
            'resolved_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'status' => 'banned',
        ]);

        $this->assertDatabaseHas('profiles', [
            'id' => $targetProfile->id,
            'status' => 'banned',
        ]);

        Mail::assertSent(ReportBanThankYou::class, function (ReportBanThankYou $mail) use ($reporterUser) {
            return $mail->hasTo($reporterUser->email);
        });

        Mail::assertSent(ReportBannedNotice::class, function (ReportBannedNotice $mail) use ($targetUser) {
            return $mail->hasTo($targetUser->email);
        });
    }

    public function test_admin_can_mark_report_safe(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $reporter = User::factory()->create();
        $reporterProfile = Profile::factory()->create([
            'user_id' => $reporter->id,
            'display_id' => 'RP4001',
            'status' => 'active',
        ]);

        $target = User::factory()->create();
        $targetProfile = Profile::factory()->create([
            'user_id' => $target->id,
            'display_id' => 'RP4002',
            'status' => 'active',
        ]);

        $report = Report::create([
            'reporter_profile_id' => $reporterProfile->id,
            'target_profile_id' => $targetProfile->id,
            'category' => 'Other',
            'details' => 'I want to report this profile due to suspicious behavior.',
            'status' => 'open',
        ]);

        app(ReportModerationService::class)->markSafe($report, $admin);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'safe',
            'resolved_by' => $admin->id,
        ]);
    }
}
