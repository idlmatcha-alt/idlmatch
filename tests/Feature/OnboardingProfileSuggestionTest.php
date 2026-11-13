<?php

namespace Tests\Feature;

use App\Jobs\SendDailyEngagementProfileSuggestions;
use App\Jobs\SendSingleProfileSuggestionEmail;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OnboardingProfileSuggestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_female_signup_queues_ten_male_profile_suggestion_emails_and_daily_campaign(): void
    {
        Mail::fake();
        Queue::fake();

        $this->createCandidateProfiles('male', 14);

        $response = $this->post('/register', $this->registrationPayload([
            'email' => 'female-new@example.com',
            'gender' => 'female',
            'first_name' => 'Female',
            'last_name' => 'Member',
            'mobile_no' => '+923001200001',
        ]));

        $response->assertRedirect(route('profile.verify'));

        Queue::assertPushed(SendSingleProfileSuggestionEmail::class, 5);
        Queue::assertNotPushed(SendSingleProfileSuggestionEmail::class, function (SendSingleProfileSuggestionEmail $job) {
            $suggested = Profile::query()->find($job->suggestedProfileId);
            return strtolower((string) $suggested?->gender) !== 'male';
        });
        Queue::assertNotPushed(SendSingleProfileSuggestionEmail::class, function (SendSingleProfileSuggestionEmail $job) {
            if ($job->campaign !== 'initial') {
                return false;
            }

            $delay = $job->delay;
            if (!$delay instanceof \DateTimeInterface) {
                return true;
            }

            return Carbon::instance($delay)->gt(now()->addMinutes(8));
        });

        $newUser = User::query()->where('email', 'female-new@example.com')->firstOrFail();
        $newProfile = $newUser->profile()->firstOrFail();

        Queue::assertPushed(SendDailyEngagementProfileSuggestions::class, function (SendDailyEngagementProfileSuggestions $job) use ($newProfile) {
            return $job->profileId === $newProfile->id
                && $job->daysRemaining === 7
                && $job->minCount === 5
                && $job->maxCount === 10;
        });
    }

    public function test_male_signup_queues_ten_female_profile_suggestion_emails_and_daily_campaign(): void
    {
        Mail::fake();
        Queue::fake();

        $this->createCandidateProfiles('female', 14);

        $response = $this->post('/register', $this->registrationPayload([
            'email' => 'male-new@example.com',
            'gender' => 'male',
            'first_name' => 'Male',
            'last_name' => 'Member',
            'mobile_no' => '+923001200002',
        ]));

        $response->assertRedirect(route('profile.verify'));

        Queue::assertPushed(SendSingleProfileSuggestionEmail::class, 5);
        Queue::assertNotPushed(SendSingleProfileSuggestionEmail::class, function (SendSingleProfileSuggestionEmail $job) {
            $suggested = Profile::query()->find($job->suggestedProfileId);
            return strtolower((string) $suggested?->gender) !== 'female';
        });
        Queue::assertNotPushed(SendSingleProfileSuggestionEmail::class, function (SendSingleProfileSuggestionEmail $job) {
            if ($job->campaign !== 'initial') {
                return false;
            }

            $delay = $job->delay;
            if (!$delay instanceof \DateTimeInterface) {
                return true;
            }

            return Carbon::instance($delay)->gt(now()->addMinutes(8));
        });

        $newUser = User::query()->where('email', 'male-new@example.com')->firstOrFail();
        $newProfile = $newUser->profile()->firstOrFail();

        Queue::assertPushed(SendDailyEngagementProfileSuggestions::class, function (SendDailyEngagementProfileSuggestions $job) use ($newProfile) {
            return $job->profileId === $newProfile->id
                && $job->daysRemaining === 7
                && $job->minCount === 5
                && $job->maxCount === 10;
        });
    }

    private function createCandidateProfiles(string $gender, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $user = User::factory()->create([
                'email' => "{$gender}{$i}@example.com",
                'role' => 'member',
                'status' => 'active',
            ]);

            Profile::factory()->create([
                'user_id' => $user->id,
                'display_id' => Profile::generateUniqueDisplayId(),
                'gender' => $gender,
                'status' => 'active',
                'city' => 'Lahore',
                'country' => 'Pakistan',
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'profile_for' => 'Myself',
            'gender' => 'male',
            'dob' => '1992-01-01',
            'mobile_no' => '+923001234567',
            'country' => 'Pakistan',
            'state' => 'Punjab',
            'city' => 'Lahore',
            'marital_status' => 'Single',
            'height_cm' => 172,
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
            'declaration_agreement' => 'agree',
        ], $overrides);
    }
}
