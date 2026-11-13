<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendDailyEngagementProfileSuggestions;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\OnboardingProfileSuggestionService;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Support\Tracking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $socialData = session('social_registration');
        return view('auth.register', compact('socialData'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $socialData = session('social_registration');
        $isSocial = $socialData && ($socialData['email'] === $request->email);

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'profile_for' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'string'],
            'dob' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'mobile_no' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
            'marital_status' => ['required', 'string'],
            'mother_tongue' => ['required', 'string'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'brothers_count' => ['nullable', 'integer', 'min:0', 'max:20'],
            'married_brothers_count' => ['nullable', 'integer', 'min:0', 'lte:brothers_count'],
            'sisters_count' => ['nullable', 'integer', 'min:0', 'max:20'],
            'married_sisters_count' => ['nullable', 'integer', 'min:0', 'lte:sisters_count'],
            'married_siblings_count' => ['nullable', 'integer', 'min:0'],
            'disability' => ['nullable', 'string', 'max:100'],
            'weight_kg' => ['nullable', 'integer', 'min:30', 'max:200'],
            'looking_to_marry' => ['nullable', 'string', 'max:100'],
            'willing_to_relocate' => ['nullable', 'string', 'max:100'],
            'is_revert' => ['nullable', 'boolean'],
            'university' => ['nullable', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', 'max:255'],
            'declaration_agreement' => ['required', 'in:agree'],
        ];

        if (!$isSocial) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules, [
            'declaration_agreement.required' => 'Please confirm the declaration before creating your account.',
            'declaration_agreement.in' => 'You must agree to the declaration to access and use this website.',
        ]);

        $brothersCount = max(0, (int) $request->input('brothers_count', 0));
        $sistersCount = max(0, (int) $request->input('sisters_count', 0));
        $marriedBrothersCount = min($brothersCount, max(0, (int) $request->input('married_brothers_count', 0)));
        $marriedSistersCount = min($sistersCount, max(0, (int) $request->input('married_sisters_count', 0)));
        $marriedSiblingsCount = $marriedBrothersCount + $marriedSistersCount;

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => $isSocial ? Hash::make(Str::random(24)) : Hash::make($request->password),
            'google_id' => $isSocial ? $socialData['google_id'] : null,
            'avatar' => $isSocial ? $socialData['avatar'] : null,
            'email_verified_at' => $isSocial ? now() : null,
        ]);

        if ($isSocial) {
            session()->forget('social_registration');
        }

        $religion = trim((string) $request->input('religion', ''));
        if ($religion === '') {
            $religion = 'Islam';
        }

        $motherOccupation = trim((string) $request->input('mother_occupation', ''));
        if ($motherOccupation === '') {
            $motherOccupation = 'Housewife';
        }

        $profileData = [
            'user_id' => $user->id,
            'display_id' => \App\Models\Profile::generateUniqueDisplayId(),
            'profile_for' => $request->profile_for,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'mobile_no' => $request->mobile_no,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'home_location' => $request->home_location,
            'home_status' => $request->home_status,
            'home_size' => $request->home_size,
            'marital_status' => $request->marital_status,
            'education' => $request->education,
            'major_course' => $request->major_course,
            'profession' => $request->job_title, // mapping job_title to profession
            'job_type' => $request->job_type,
            'university' => $request->university,
            'income' => $request->income,
            'religion' => $religion,
            'sect' => $request->sect,
            'is_revert' => $request->is_revert,
            'caste' => $request->caste,
            'mother_tongue' => $request->mother_tongue,
            'father_name' => $request->father_name,
            'father_occupation' => $request->father_occupation,
            'mother_occupation' => $motherOccupation,
            'brothers_count' => $brothersCount,
            'sisters_count' => $sistersCount,
            'married_siblings_count' => $marriedSiblingsCount,
            'disability' => $request->disability,
            'height_cm' => $request->height_cm,
            'weight_kg' => $request->weight_kg,
            'looking_to_marry' => $request->looking_to_marry,
            'willing_to_relocate' => $request->willing_to_relocate,
            'privacy_json' => ['visibility' => $request->profile_visibility],
            'status' => 'active', // or under_review
        ];

        if (Schema::hasColumn('profiles', 'married_brothers_count') && Schema::hasColumn('profiles', 'married_sisters_count')) {
            $profileData['married_brothers_count'] = $marriedBrothersCount;
            $profileData['married_sisters_count'] = $marriedSistersCount;
        }

        $profile = \App\Models\Profile::create($profileData);

        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            Log::error('Failed to send Registration email: ' . $e->getMessage());
        }

        try {
            /** @var OnboardingProfileSuggestionService $suggestionService */
            $suggestionService = app(OnboardingProfileSuggestionService::class);
            $initialCount = max(1, (int) config('profile_suggestions.initial.count', 10));
            $initialWindowMinutes = max(1, (int) config('profile_suggestions.initial.window_minutes', 8));
            $sentProfileIds = $suggestionService->queueInitialSuggestions(
                $profile,
                $initialCount,
                $initialWindowMinutes
            );

            if ((bool) config('profile_suggestions.daily_engagement.enabled', true)) {
                $dailyStartAfterDays = max(1, (int) config('profile_suggestions.daily_engagement.start_after_days', 1));
                $dailyCampaignDays = max(1, (int) config('profile_suggestions.daily_engagement.campaign_days', 7));
                $dailyMinCount = max(1, (int) config('profile_suggestions.daily_engagement.min_count', 2));
                $dailyMaxCount = max($dailyMinCount, (int) config('profile_suggestions.daily_engagement.max_count', 4));

                SendDailyEngagementProfileSuggestions::dispatch(
                    $profile->id,
                    $dailyCampaignDays,
                    $dailyMinCount,
                    $dailyMaxCount,
                    $sentProfileIds
                )
                    ->onConnection('database')
                    ->onQueue('emails')
                    ->delay(now()->addDays($dailyStartAfterDays));
            }
        } catch (\Throwable $exception) {
            Log::error(
                'Failed to schedule onboarding profile suggestion emails for user id '
                . $user->id . ': ' . $exception->getMessage()
            );
        }

        Auth::login($user);

        Tracking::meta('CompleteRegistration');

        return redirect()->route('profile.verify'); // Direct to Identity Verification
    }
}
