<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\Profile;
use App\Models\Report;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(ReportRequest $request, string $displayId)
    {
        $profile = $request->user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        if ((string) $profile->display_id === $displayId) {
            return redirect()->back()->withErrors(['report' => 'You cannot report your own profile.']);
        }

        $target = Profile::visibleToUsers()
            ->where('display_id', $displayId)
            ->firstOrFail();

        $hasOpenReport = Report::where('reporter_profile_id', $profile->id)
            ->where('target_profile_id', $target->id)
            ->where('status', 'open')
            ->exists();

        if ($hasOpenReport) {
            return redirect()->back()->withErrors(['report' => 'You already submitted an open report for this profile.']);
        }

        Report::create([
            'reporter_profile_id' => $profile->id,
            'target_profile_id' => $target->id,
            'category' => $request->input('category'),
            'details' => $request->input('details'),
            'status' => 'open',
        ]);

        return redirect()->back()->with('status', 'Report submitted.');
    }
}
