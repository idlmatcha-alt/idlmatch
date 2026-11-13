<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\Profile;

class ProfileSectionController extends Controller
{
    private const GALLERY_LIMIT = 4;

    public function update(
        Request $request,
        \App\Services\ImageService $imageService,
        \App\Services\ProfileCompletionService $completionService,
        string $section
    )
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            abort(404, 'Profile not found.');
        }

        switch ($section) {
            case 'personal':
                $validated = $request->validate([
                    'gender' => ['required', Rule::in(['male', 'female'])],
                    'date_of_birth' => ['required', 'date', 'before:today'],
                    'country' => ['nullable', 'string'],
                    'state' => ['nullable', 'string'],
                    'city' => ['nullable', 'string'],
                    'marital_status' => ['nullable', 'string'],
                    'mother_tongue' => ['nullable', 'string'],
                    'caste' => ['nullable', 'string'],
                    'origin_country' => ['nullable', 'string'],
                    'origin_state' => ['nullable', 'string'],
                    'origin_city' => ['nullable', 'string'],
                    'willing_to_relocate' => ['nullable', 'string'],
                    'looking_to_marry' => ['nullable', 'string'],
                    'home_status' => ['nullable', 'string'], // Living Arrangement
                    'height_cm' => ['nullable', 'numeric'],
                    'weight_kg' => ['nullable', 'numeric'],
                    'disability' => ['nullable', 'string'],
                ]);
                if (isset($validated['date_of_birth'])) {
                    $validated['dob'] = $validated['date_of_birth'];
                    unset($validated['date_of_birth']);
                }
                $profile->update($validated);
                break;

            case 'religion':
                $validated = $request->validate([
                    'religion' => ['nullable', 'string'],
                    'sect' => ['nullable', 'string'],
                    'religiousness' => ['nullable', 'string'],
                    'is_revert' => ['nullable', 'boolean'],
                ]);
                $profile->update($validated);
                break;

            case 'family':
                $validated = $request->validate([
                    'father_name' => ['nullable', 'string', 'max:100'],
                    'father_occupation' => ['nullable', 'string', 'max:100'],
                    'mother_occupation' => ['nullable', 'string', 'max:100'],
                    'brothers_count' => ['nullable', 'integer', 'min:0', 'max:20'],
                    'married_brothers_count' => ['nullable', 'integer', 'min:0', 'lte:brothers_count'],
                    'sisters_count' => ['nullable', 'integer', 'min:0', 'max:20'],
                    'married_sisters_count' => ['nullable', 'integer', 'min:0', 'lte:sisters_count'],
                    'married_siblings_count' => ['nullable', 'integer', 'min:0'],
                ]);

                $brothersCount = max(0, (int) ($validated['brothers_count'] ?? $profile->brothers_count ?? 0));
                $sistersCount = max(0, (int) ($validated['sisters_count'] ?? $profile->sisters_count ?? 0));
                $inputMarriedTotal = max(0, (int) ($validated['married_siblings_count'] ?? $profile->married_siblings_count ?? 0));
                $hasSeparatedMarriedCounts = array_key_exists('married_brothers_count', $validated) || array_key_exists('married_sisters_count', $validated);

                if ($hasSeparatedMarriedCounts) {
                    $marriedBrothersCount = min($brothersCount, max(0, (int) ($validated['married_brothers_count'] ?? $profile->married_brothers_count ?? 0)));
                    $marriedSistersCount = min($sistersCount, max(0, (int) ($validated['married_sisters_count'] ?? $profile->married_sisters_count ?? 0)));
                } else {
                    $marriedBrothersCount = min($brothersCount, $inputMarriedTotal);
                    $marriedSistersCount = min($sistersCount, max(0, $inputMarriedTotal - $marriedBrothersCount));
                }

                $validated['brothers_count'] = $brothersCount;
                $validated['sisters_count'] = $sistersCount;

                if (Schema::hasColumn('profiles', 'married_brothers_count') && Schema::hasColumn('profiles', 'married_sisters_count')) {
                    $validated['married_brothers_count'] = $marriedBrothersCount;
                    $validated['married_sisters_count'] = $marriedSistersCount;
                    $validated['married_siblings_count'] = $marriedBrothersCount + $marriedSistersCount;
                } else {
                    unset($validated['married_brothers_count'], $validated['married_sisters_count']);
                    $validated['married_siblings_count'] = $inputMarriedTotal;
                }

                $profile->update($validated);
                break;

            case 'property':
                $validated = $request->validate([
                    'home_status' => ['nullable', 'string'],
                    'home_size' => ['nullable', 'string'],
                    'other_properties' => ['nullable', 'string'],
                ]);
                $profile->update($validated);
                break;

            case 'address':
                $validated = $request->validate([
                    'country' => ['nullable', 'string'],
                    'state' => ['nullable', 'string'],
                    'city' => ['nullable', 'string'],
                    'home_town' => ['nullable', 'string'],
                    'origin_country' => ['nullable', 'string'],
                    'origin_state' => ['nullable', 'string'],
                    'origin_city' => ['nullable', 'string'],
                    'home_location' => ['nullable', 'string'],
                    'mobile_no' => ['nullable', 'string'],
                ]);
                $profile->update($validated);
                break;

            case 'career':
                $validated = $request->validate([
                    'education' => ['nullable', 'string'], // Qualification
                    'major_course' => ['nullable', 'string'],
                    'profession' => ['nullable', 'string'], // Occupation
                    'job_type' => ['nullable', 'string'], // Job Post
                    'nature_of_job' => ['nullable', 'string'], // Job Post
                    'income' => ['nullable', 'string'],
                    'college' => ['nullable', 'string'],
                    'university' => ['nullable', 'string'],
                ]);

                // Keep both legacy/current fields aligned so completion and UI stay consistent.
                if (!empty($validated['nature_of_job']) && empty($validated['job_type'])) {
                    $validated['job_type'] = $validated['nature_of_job'];
                }
                if (!empty($validated['job_type']) && empty($validated['nature_of_job'])) {
                    $validated['nature_of_job'] = $validated['job_type'];
                }

                $profile->update($validated);
                break;

            case 'hobbies':
                $validated = $request->validate([
                    'hobbies' => ['nullable', 'string'], // Comma-separated or array handled by accessor?
                ]);
                // Check if hobbies is array or string. View implies array explode.
                // Assuming simple string storage for now based on View's "explode" logic
                $profile->update($validated);
                break;

            case 'about':
            case 'bio':
                 $validated = $request->validate([
                    'bio' => ['nullable', 'string', 'max:1000'],
                ]);
                $profile->update($validated);
                break;
            
            case 'expectation':
                 $validated = $request->validate([
                    'desired_gender' => ['nullable', 'string'],
                    'desired_age_min' => ['nullable', 'integer'],
                    'desired_age_max' => ['nullable', 'integer'],
                    'desired_country' => ['nullable', 'string'],
                    'desired_state' => ['nullable', 'string'],
                    'desired_city' => ['nullable', 'string'],
                    'desired_mother_tongue' => ['nullable', 'string'],
                    'desired_religiousness' => ['nullable', 'string'],
                    'desired_religion' => ['nullable', 'string'],
                    'desired_sect' => ['nullable', 'string'],
                    'desired_education' => ['nullable', 'string'],
                    'desired_profession' => ['nullable', 'string'],
                    'desired_willing_to_relocate' => ['nullable', 'string'],
                    'desired_income' => ['nullable', 'string'],
                    'desired_marital_status' => ['nullable', 'string'],
                    'desired_living_arrangement' => ['nullable', 'string'],
                    'desired_height_min' => ['nullable', 'numeric'],
                    'desired_height_max' => ['nullable', 'numeric'],
                    'desired_disability' => ['nullable', 'string'],
                    'desired_caste' => ['nullable', 'string'],
                 ]);
                 
                 $profile->preferences()->updateOrCreate(
                     ['profile_id' => $profile->id],
                     $validated
                 );
                 break;

            case 'photo':
                $request->validate([
                    'photo' => ['required', 'image', 'max:10240'],
                ]);

                if ($request->hasFile('photo')) {
                    $path = $imageService->optimizeProfile($request->file('photo'), 'profiles/' . $profile->id);
                    $existingPrimary = $profile->photos()->where('is_primary', true)->latest('id')->first();

                    if ($existingPrimary) {
                        $oldPath = $existingPrimary->path;
                        $existingPrimary->update([
                            'path' => $path,
                            'status' => 'approved',
                            'is_primary' => true,
                        ]);

                        if ($oldPath !== $path) {
                            $imageService->delete($oldPath);
                        }

                        $primaryPhotoId = $existingPrimary->id;
                    } else {
                        $newPrimary = \App\Models\Photo::create([
                            'profile_id' => $profile->id,
                            'path' => $path,
                            'status' => 'approved',
                            'is_primary' => true,
                        ]);
                        $primaryPhotoId = $newPrimary->id;
                    }

                    $profile->photos()
                        ->where('id', '!=', $primaryPhotoId)
                        ->where('is_primary', true)
                        ->update(['is_primary' => false]);
                }
                break;

            case 'gallery':
                $request->validate([
                    'photo' => ['required', 'image', 'max:10240'],
                ]);

                $galleryCount = $profile->photos()->where('is_primary', false)->count();
                if ($galleryCount >= self::GALLERY_LIMIT) {
                    return redirect()
                        ->route('profiles.show', $profile->display_id)
                        ->with('error', 'You can upload up to ' . self::GALLERY_LIMIT . ' gallery photos. Delete one photo to add another.');
                }

                if ($request->hasFile('photo')) {
                    $path = $imageService->optimizeProfile($request->file('photo'), 'profiles/' . $profile->id);

                    \App\Models\Photo::create([
                        'profile_id' => $profile->id,
                        'path' => $path,
                        'status' => 'approved',
                        'is_primary' => false,
                    ]);
                }
                break;

            default:
                abort(400, 'Invalid section.');
        }

        $profile->update([
            'completion_score' => $completionService->calculate($profile),
        ]);

        return redirect()->route('profiles.show', $profile->display_id)->with('status', ucfirst($section) . ' information updated.');
    }
}
