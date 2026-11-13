<?php

namespace App\Services;

use App\Models\Profile;

class ProfileCompletionService
{
    public function calculate(Profile $profile): int
    {
        $checks = $this->completionChecks();
        $totalChecks = count($checks);

        if ($totalChecks === 0) {
            return 0;
        }

        $filledChecks = 0;
        foreach ($checks as $check) {
            if (($check['filled'])($profile)) {
                $filledChecks++;
            }
        }

        return (int) round(($filledChecks / $totalChecks) * 100);
    }

    public function prompts(Profile $profile): array
    {
        return array_values(array_unique(array_map(
            fn (array $item): string => (string) ($item['prompt'] ?? $item['label'] ?? 'Complete pending profile fields'),
            $this->pendingItems($profile)
        )));
    }

    /**
     * Pending items used by dashboard popup quick-complete UI.
     *
     * @return array<int, array<string, mixed>>
     */
    public function pendingItems(Profile $profile): array
    {
        $items = [];
        foreach ($this->completionChecks() as $check) {
            if (($check['filled'])($profile)) {
                continue;
            }

            $valueResolver = $check['value'] ?? fn (Profile $p) => data_get($p, (string) ($check['target_key'] ?? $check['key']), '');

            $items[] = [
                'key' => (string) ($check['target_key'] ?? $check['key']),
                'label' => (string) ($check['label'] ?? 'Pending Field'),
                'prompt' => (string) ($check['prompt'] ?? 'Please complete this field.'),
                'type' => (string) ($check['type'] ?? 'text'),
                'options' => (array) ($check['options'] ?? []),
                'value' => $valueResolver($profile),
                'editable' => (bool) ($check['editable'] ?? true),
                'action_route' => $check['action_route'] ?? null,
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function completionChecks(): array
    {
        return [
            $this->fieldCheck('disability', 'Disability', 'Add disability details if applicable'),
            $this->fieldCheck('bio', 'Bio', 'Write a short bio about yourself', 'textarea'),
            $this->fieldCheck('religion', 'Religion', 'Add your religion'),
            $this->fieldCheck('sect', 'Sect', 'Add your sect'),
            $this->fieldCheck('caste', 'Caste', 'Add your caste'),
            $this->fieldCheck('mother_tongue', 'Mother Tongue', 'Add your mother tongue'),
            $this->fieldCheck('education', 'Education', 'Add your education details'),
            $this->fieldCheck('major_course', 'Major Course', 'Add your major course'),
            $this->fieldCheck('college', 'College', 'Add your college'),
            $this->fieldCheck('university', 'University', 'Add your university'),
            $this->fieldCheck('profession', 'Profession', 'Add your profession'),
            [
                'key' => 'job_post',
                'target_key' => 'job_type',
                'label' => 'Job Post',
                'prompt' => 'Add your professional designation',
                'type' => 'text',
                'editable' => true,
                'filled' => fn (Profile $profile) => $this->hasValue($profile->job_type) || $this->hasValue($profile->nature_of_job),
                'value' => fn (Profile $profile) => (string) ($profile->job_type ?: $profile->nature_of_job ?: ''),
            ],
            $this->fieldCheck('income', 'Income', 'Add your income details'),
            $this->fieldCheck('father_name', 'Father Name', 'Add your father name'),
            $this->fieldCheck('father_occupation', 'Father Occupation', 'Add your father occupation'),
            $this->fieldCheck('mother_occupation', 'Mother Occupation', 'Add your mother occupation'),
            $this->fieldCheck('brothers_count', 'Brothers Count', 'Add your family details', 'number'),
            $this->fieldCheck('sisters_count', 'Sisters Count', 'Add your family details', 'number'),
            $this->fieldCheck('married_siblings_count', 'Married Siblings Count', 'Add married siblings count', 'number'),
            $this->fieldCheck('city', 'City', 'Add your city'),
            $this->fieldCheck('home_town', 'Home Town', 'Add your home town'),
            $this->fieldCheck('home_location', 'Home Location', 'Add your home location'),
            $this->fieldCheck('mobile_no', 'Mobile Number', 'Add your mobile number'),
            $this->fieldCheck('home_status', 'Home Status', 'Add your home status (Own/Rent)', 'select', true, [
                'Own' => 'Own',
                'Rent' => 'Rent',
                'Sharing' => 'Sharing',
                'own' => 'Own',
                'rented' => 'Rented',
                'sharing' => 'Sharing',
            ]),
            $this->fieldCheck('home_size', 'Home Size', 'Add your home size'),
            $this->fieldCheck('other_properties', 'Other Properties', 'Add your other properties'),
            $this->fieldCheck('gender', 'Gender', 'Add your gender', 'select', true, [
                'male' => 'Male',
                'female' => 'Female',
            ]),
            $this->fieldCheck('dob', 'Date of Birth', 'Add your date of birth', 'date'),
            $this->fieldCheck('marital_status', 'Marital Status', 'Add your marital status'),
            $this->fieldCheck('height_cm', 'Height (cm)', 'Add your height in cm', 'number'),
            [
                'key' => 'preferences',
                'target_key' => 'preferences',
                'label' => 'Partner Preferences',
                'prompt' => 'Set your partner preferences',
                'type' => 'action',
                'editable' => false,
                'action_route' => 'profile.preferences',
                'filled' => fn (Profile $profile) => $profile->preferences()->exists(),
                'value' => fn () => '',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fieldCheck(
        string $key,
        string $label,
        string $prompt,
        string $type = 'text',
        bool $editable = true,
        array $options = []
    ): array {
        return [
            'key' => $key,
            'target_key' => $key,
            'label' => $label,
            'prompt' => $prompt,
            'type' => $type,
            'editable' => $editable,
            'options' => $options,
            'filled' => fn (Profile $profile) => $this->hasValue($profile->{$key}),
            'value' => fn (Profile $profile) => $profile->{$key},
        ];
    }

    private function hasValue($value): bool
    {
        return !empty($value) || $value === 0 || $value === '0';
    }
}
