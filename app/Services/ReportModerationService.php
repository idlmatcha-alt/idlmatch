<?php

namespace App\Services;

use App\Mail\ReportBanThankYou;
use App\Mail\ReportBannedNotice;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReportModerationService
{
    public function markSafe(Report $report, ?User $admin = null): Report
    {
        $report->forceFill([
            'status' => 'safe',
            'resolved_by' => $admin?->id,
            'resolved_at' => now(),
        ])->save();

        return $report->refresh();
    }

    public function banTarget(Report $report, User $admin): Report
    {
        $report->loadMissing(['reporter.user', 'target.user']);

        $targetProfile = $report->target;
        if (!$targetProfile) {
            return $report;
        }

        $resolvedAt = now();

        DB::transaction(function () use ($report, $admin, $targetProfile, $resolvedAt) {
            // Close all currently open reports for the same target with "banned" decision.
            Report::query()
                ->where('target_profile_id', $targetProfile->id)
                ->where('status', 'open')
                ->update([
                    'status' => 'banned',
                    'resolved_by' => $admin->id,
                    'resolved_at' => $resolvedAt,
                    'updated_at' => $resolvedAt,
                ]);

            $report->forceFill([
                'status' => 'banned',
                'resolved_by' => $admin->id,
                'resolved_at' => $resolvedAt,
            ])->save();

            if ($targetProfile->user) {
                $targetProfile->user->forceFill(['status' => 'banned'])->save();
            }

            $targetProfile->forceFill(['status' => 'banned'])->save();
        });

        $report->refresh()->loadMissing(['reporter.user', 'target.user']);
        $this->sendBanEmails($report);

        return $report;
    }

    protected function sendBanEmails(Report $report): void
    {
        $reporterUser = $report->reporter?->user;
        $targetUser = $report->target?->user;

        if ($reporterUser?->email) {
            rescue(fn () => Mail::to($reporterUser->email)->send(new ReportBanThankYou($report)));
        }

        if ($targetUser?->email) {
            rescue(fn () => Mail::to($targetUser->email)->send(new ReportBannedNotice($report)));
        }
    }
}
