<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileStepOneRequest;
use App\Http\Requests\ProfileStepTwoRequest;
use App\Http\Requests\ProfileStepThreeRequest;
use App\Http\Requests\ProfileStepFourRequest;
use App\Http\Requests\ProfileStepFiveRequest;
use App\Http\Requests\ProfileStepSixRequest;
use App\Http\Requests\ProfileStepSevenRequest;
use App\Http\Requests\ProfileStepEightRequest;
use App\Models\Preference;
use App\Models\Profile;
use App\Models\Photo;
use App\Services\ProfileCompletionService;
use App\Services\ImageService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class ProfileWizardController extends Controller
{
    private const GALLERY_LIMIT = 4;
    private const MOBILE_UPLOAD_TYPES = ['photo', 'gallery', 'selfie', 'cnic_front', 'cnic_back'];

    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except([
            'showMobileUpload', 
            'storeMobileUpload'
        ]);
    }

    protected function getOrCreateProfile(): Profile
    {
        $user = auth()->user();
        if (!$user->profile) {
            Profile::create([
                'user_id' => $user->id,
                'display_id' => Profile::generateUniqueDisplayId(),
                'gender' => 'male', // Default
                'dob' => now()->subYears(25),
                'status' => 'active',
            ]);
        }
        return $user->profile()->first();
    }

    protected function updateCompletionScore(Profile $profile, ProfileCompletionService $completionService)
    {
        $profile->update([
            'completion_score' => $completionService->calculate($profile)
        ]);
    }

    protected function storePhoto(Profile $profile, $photoFile, ImageService $imageService)
    {
        // Store higher-quality profile images for sharper rendering across cards/detail pages.
        $path = $imageService->optimizeProfile($photoFile, 'profiles/' . $profile->id);

        $photo = new Photo([
            'profile_id' => $profile->id,
            'path' => $path,
            'status' => 'approved',
            'is_primary' => !$profile->photos()->exists(),
        ]);

        $photo->save();
    }

    protected function storeGalleryPhoto(Profile $profile, $photoFile, ImageService $imageService): void
    {
        $path = $imageService->optimizeProfile($photoFile, 'profiles/' . $profile->id);

        Photo::create([
            'profile_id' => $profile->id,
            'path' => $path,
            'status' => 'approved',
            'is_primary' => false,
        ]);
    }

    private function normalizeMobileUploadType(?string $type): string
    {
        $normalizedType = (string) $type;

        return in_array($normalizedType, self::MOBILE_UPLOAD_TYPES, true)
            ? $normalizedType
            : 'photo';
    }

    /**
     * Returns true when the upload affects verification status (selfie/CNIC), false for profile/gallery photos.
     */
    private function handleMobilePhotoUpload(Profile $profile, string $type, Request $request, ImageService $imageService): bool
    {
        if (!$request->hasFile('photo')) {
            return false;
        }

        switch ($type) {
            case 'selfie':
                $profile->selfie = $imageService->optimize($request->file('photo'), 'verification/' . $profile->id);
                return true;

            case 'cnic_front':
                $profile->cnic_front = $imageService->optimize($request->file('photo'), 'verification/' . $profile->id);
                return true;

            case 'cnic_back':
                $profile->cnic_back = $imageService->optimize($request->file('photo'), 'verification/' . $profile->id);
                return true;

            case 'gallery':
                $this->storeGalleryPhoto($profile, $request->file('photo'), $imageService);
                return false;

            case 'photo':
            default:
                $this->storePhoto($profile, $request->file('photo'), $imageService);
                return false;
        }
    }

    public function stepOne()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-1', compact('profile'));
    }

    public function storeStepOne(ProfileStepOneRequest $request, ProfileCompletionService $completionService, ImageService $imageService)
    {
        $profile = $this->getOrCreateProfile();
        $profile->update($request->validated());

        // Handle Photo if uploaded
        if ($request->hasFile('photo')) {
            $this->storePhoto($profile, $request->file('photo'), $imageService);
        }

        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('profile.wizard.step-2');
    }

    public function stepTwo()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-2', compact('profile'));
    }

    public function storeStepTwo(ProfileStepTwoRequest $request, ProfileCompletionService $completionService, ImageService $imageService)
    {
        $profile = $this->getOrCreateProfile();
        
        $profile->update($request->validated());

        if ($request->hasFile('photo')) {
            $this->storePhoto($profile, $request->file('photo'), $imageService);
        }

        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('profile.wizard.step-3');
    }

    public function stepThree()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-3', compact('profile'));
    }

    public function storeStepThree(Request $request, ProfileCompletionService $completionService, ImageService $imageService)
    {
        $request->validate([
            'cnic_front' => 'required|image|max:5120',
            'cnic_back' => 'required|image|max:5120',
            'selfie' => 'required|image|max:5120',
        ]);

        $profile = $this->getOrCreateProfile();

        if ($request->hasFile('cnic_front')) {
            $profile->cnic_front = $imageService->optimize($request->file('cnic_front'), 'verification/' . $profile->id);
        }
        if ($request->hasFile('cnic_back')) {
            $profile->cnic_back = $imageService->optimize($request->file('cnic_back'), 'verification/' . $profile->id);
        }
        if ($request->hasFile('selfie')) {
            $profile->selfie = $imageService->optimize($request->file('selfie'), 'verification/' . $profile->id);
        }

        $profile->verification_status = 'pending';
        $profile->save();

        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('dashboard')->with('status', 'Verification documents uploaded successfully!');
    }

    public function stepFour()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-4', compact('profile'));
    }

    public function storeStepFour(ProfileStepFourRequest $request, ProfileCompletionService $completionService)
    {
        $profile = $this->getOrCreateProfile();
        $profile->update($request->validated());
        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('profile.wizard.step-5');
    }

    public function stepFive()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-5', compact('profile'));
    }

    public function storeStepFive(ProfileStepFiveRequest $request, ProfileCompletionService $completionService)
    {
        $profile = $this->getOrCreateProfile();
        $profile->update($request->validated());
        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('profile.wizard.step-6');
    }

    public function stepSix()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-6', compact('profile'));
    }

    public function storeStepSix(ProfileStepSixRequest $request, ProfileCompletionService $completionService)
    {
        $profile = $this->getOrCreateProfile();
        $profile->update($request->validated());
        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('profile.wizard.step-7');
    }

    public function stepSeven()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-7', compact('profile'));
    }

    public function storeStepSeven(ProfileStepSevenRequest $request, ProfileCompletionService $completionService)
    {
        $profile = $this->getOrCreateProfile();
        $profile->update($request->validated());
        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('profile.wizard.step-8');
    }

    public function stepEight()
    {
        $profile = $this->getOrCreateProfile();
        $preferences = $profile->preferences ?: new Preference();

        return view('profile.wizard.step-8', [
            'profile' => $profile,
            'preferences' => $preferences,
            'religions' => \App\Helpers\FormOptions::religions(),
            'sects' => \App\Helpers\FormOptions::sects(),
            'castes' => \App\Helpers\FormOptions::castes(),
            'marital_statuses' => \App\Helpers\FormOptions::maritalStatuses(),
            'education_levels' => \App\Helpers\FormOptions::educationLevels(),
            'cities' => \App\Helpers\FormOptions::cities(),
            'mother_tongues' => \App\Helpers\FormOptions::motherTongues(),
            'relocation_options' => \App\Helpers\FormOptions::relocationOptions(),
            'religiousness_levels' => \App\Helpers\FormOptions::religiousnessLevels(),
            'living_arrangements' => \App\Helpers\FormOptions::livingArrangements(),
            'disability_options' => \App\Helpers\FormOptions::disabilityOptions(),
        ]);
    }

    public function storeStepEight(ProfileStepEightRequest $request, ProfileCompletionService $completionService)
    {
        $profile = $this->getOrCreateProfile();
        $preferences = $profile->preferences ?: new Preference(['profile_id' => $profile->id]);
        $preferences->fill($request->validated());
        $preferences->save();

        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('packages.index');
    }

    public function complete()
    {
        return redirect()->route('profile.complete-setup');
    }

    public function showPhotos()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-photos', compact('profile'));
    }

    public function storePhotos(Request $request, ProfileCompletionService $completionService, ImageService $imageService)
    {

        $request->validate([
            'photo' => 'required|image|max:10240',
        ]);

        $profile = $this->getOrCreateProfile();

        if ($request->hasFile('photo')) {
            $this->storePhoto($profile, $request->file('photo'), $imageService);
        }

        $this->updateCompletionScore($profile, $completionService);
        return redirect()->route('profile.preferences');
    }

    public function showVerify()
    {
        $profile = $this->getOrCreateProfile();
        
        // Generate a temporary token for mobile upload (valid for 1 hour)
        $token = Str::random(32);
        Cache::put('mobile_upload_token_' . auth()->id(), $token, now()->addHour());
        
        return view('profile.wizard.step-verify', compact('profile', 'token'));
    }

    public function storeVerify(Request $request, ProfileCompletionService $completionService, ImageService $imageService)
    {
        $profile = $this->getOrCreateProfile();

        $request->validate([
            'selfie' => ($profile->selfie ? 'nullable' : 'required') . '|image|max:5120',
            'cnic_front' => 'nullable|image|max:5120',
            'cnic_back' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('selfie')) {
            $profile->selfie = $imageService->optimize($request->file('selfie'), 'verification/' . $profile->id);
        }
        if ($request->hasFile('cnic_front')) {
            $profile->cnic_front = $imageService->optimize($request->file('cnic_front'), 'verification/' . $profile->id);
        }
        if ($request->hasFile('cnic_back')) {
            $profile->cnic_back = $imageService->optimize($request->file('cnic_back'), 'verification/' . $profile->id);
        }

        // If CNIC was uploaded, or if re-submitting after rejection, mark for verification
        if ($request->hasFile('cnic_front') || $request->hasFile('cnic_back') || $request->hasFile('selfie')) {
            $profile->verification_status = 'pending';
            $profile->rejection_reason = null; // Clear rejection reason
        }

        $profile->save();
        $this->updateCompletionScore($profile, $completionService);

        return redirect()->route('profile.photos')->with('status', 'Verification submitted! Now add a profile picture.');
    }

    public function showPreferences()
    {
        $profile = $this->getOrCreateProfile();
        return view('profile.wizard.step-preferences', [
            'profile' => $profile,
            'religions' => \App\Helpers\FormOptions::religions(),
            'sects' => \App\Helpers\FormOptions::sects(),
            'castes' => \App\Helpers\FormOptions::castes(),
            'mother_tongues' => \App\Helpers\FormOptions::motherTongues(),
            'education_levels' => \App\Helpers\FormOptions::educationLevels(),
            'living_arrangements' => \App\Helpers\FormOptions::livingArrangements(),
            'religiousness_levels' => \App\Helpers\FormOptions::religiousnessLevels(),
            'relocation_options' => \App\Helpers\FormOptions::relocationOptions(),
        ]);
    }

    public function completeProfile()
    {
        $profile = $this->getOrCreateProfile();
        $profile->loadMissing('preferences');

        $sections = [
            'Account Information' => [
                'icon' => 'user',
                'fields' => [
                    'name' => ['label' => 'Full Name', 'type' => 'text', 'source' => 'user'],
                    'email' => ['label' => 'Email', 'type' => 'email', 'source' => 'user'],
                    'phone' => ['label' => 'Phone', 'type' => 'text'],
                    'hide_contact' => [
                        'label' => 'Hide Contact Publicly',
                        'type' => 'select',
                        'options' => ['0' => 'No', '1' => 'Yes'],
                        'source' => 'privacy',
                    ],
                ],
            ],
            'Personal Details' => [
                'icon' => 'user',
                'fields' => [
                    'profile_for' => [
                        'label' => 'Profile For',
                        'type' => 'select',
                        'options' => [
                            'Myself' => 'Myself',
                            'My Son' => 'My Son',
                            'My Daughter' => 'My Daughter',
                            'My Brother' => 'My Brother',
                            'My Sister' => 'My Sister',
                            'My Friend' => 'My Friend',
                            'My Relative' => 'My Relative',
                        ],
                    ],
                    'gender' => ['label' => 'Gender', 'type' => 'select', 'options' => ['male' => 'Male', 'female' => 'Female']],
                    'dob' => ['label' => 'Date of Birth', 'type' => 'date'],
                    'height_cm' => ['label' => 'Height (cm)', 'type' => 'number', 'placeholder' => 'e.g. 170'],
                    'marital_status' => ['label' => 'Marital Status', 'type' => 'select', 'options' => \App\Helpers\FormOptions::maritalStatuses()],
                    'disability' => ['label' => 'Disability', 'type' => 'select', 'options' => \App\Helpers\FormOptions::disabilityOptions()],
                    'mother_tongue' => ['label' => 'Mother Tongue', 'type' => 'select', 'options' => \App\Helpers\FormOptions::motherTongues()],
                    'complexion' => ['label' => 'Complexion', 'type' => 'select', 'options' => \App\Helpers\FormOptions::complexions()],
                    'body_type' => ['label' => 'Body Type', 'type' => 'select', 'options' => \App\Helpers\FormOptions::bodyTypes()],
                    'eating_habits' => ['label' => 'Eating Habits', 'type' => 'select', 'options' => \App\Helpers\FormOptions::eatingHabits()],
                    'smoking_habits' => ['label' => 'Smoking Habits', 'type' => 'select', 'options' => \App\Helpers\FormOptions::smokingHabits()],
                    'drinking_habits' => ['label' => 'Drinking Habits', 'type' => 'select', 'options' => \App\Helpers\FormOptions::drinkingHabits()],
                    'religiousness' => ['label' => 'Religiousness', 'type' => 'select', 'options' => \App\Helpers\FormOptions::religiousnessLevels()],
                    'is_revert' => ['label' => 'Revert', 'type' => 'select', 'options' => ['1' => 'Yes', '0' => 'No']],
                    'hobbies' => ['label' => 'Hobbies', 'type' => 'textarea', 'placeholder' => 'Write your hobbies...'],
                    'bio' => ['label' => 'Bio', 'type' => 'textarea', 'placeholder' => 'Write a short bio...'],
                ],
            ],
            'Religion & Identity' => [
                'icon' => 'identity',
                'fields' => [
                    'religion' => ['label' => 'Religion', 'type' => 'select', 'options' => \App\Helpers\FormOptions::religions()],
                    'sect' => ['label' => 'Sect', 'type' => 'select', 'options' => \App\Helpers\FormOptions::sects()],
                    'caste' => ['label' => 'Caste', 'type' => 'select', 'options' => \App\Helpers\FormOptions::castes()],
                ],
            ],
            'Education & Career' => [
                'icon' => 'academic',
                'fields' => [
                    'education' => ['label' => 'Qualification', 'type' => 'select', 'options' => \App\Helpers\FormOptions::educationLevels()],
                    'major_course' => ['label' => 'Major Course', 'type' => 'select', 'options' => \App\Helpers\FormOptions::majorsByQualification()],
                    'college' => ['label' => 'College', 'type' => 'text'],
                    'university' => ['label' => 'University', 'type' => 'text'],
                    'profession' => ['label' => 'Profession', 'type' => 'select', 'options' => \App\Helpers\FormOptions::professions()],
                    'job_type' => ['label' => 'Job Post', 'type' => 'text'],
                    'nature_of_job' => ['label' => 'Nature of Job', 'type' => 'text'],
                    'company_name' => ['label' => 'Company Name', 'type' => 'text'],
                    'income' => ['label' => 'Monthly Income', 'type' => 'select', 'options' => \App\Helpers\FormOptions::incomeOptions()],
                ],
            ],
            'Family Details' => [
                'icon' => 'users',
                'fields' => [
                    'father_name' => ['label' => 'Father Name', 'type' => 'text'],
                    'father_occupation' => ['label' => 'Father Occupation', 'type' => 'text'],
                    'mother_occupation' => ['label' => 'Mother Occupation', 'type' => 'text'],
                    'brothers_count' => ['label' => 'Brothers Count', 'type' => 'number'],
                    'married_brothers_count' => ['label' => 'Married Brothers Count', 'type' => 'number'],
                    'sisters_count' => ['label' => 'Sisters Count', 'type' => 'number'],
                    'married_sisters_count' => ['label' => 'Married Sisters Count', 'type' => 'number'],
                    'married_siblings_count' => ['label' => 'Married Siblings Count', 'type' => 'number'],
                    'family_values' => ['label' => 'Family Values', 'type' => 'select', 'options' => \App\Helpers\FormOptions::familyValues()],
                    'family_type' => ['label' => 'Family Type', 'type' => 'select', 'options' => \App\Helpers\FormOptions::familyTypes()],
                    'family_status' => ['label' => 'Family Status', 'type' => 'select', 'options' => \App\Helpers\FormOptions::familyStatuses()],
                    'family_details' => ['label' => 'Family Details', 'type' => 'textarea', 'placeholder' => 'Add additional family details...'],
                ],
            ],
            'Contact & Location' => [
                'icon' => 'location',
                'fields' => [
                    'mobile_no' => ['label' => 'Mobile Number', 'type' => 'text'],
                    'country' => ['label' => 'Country', 'type' => 'select', 'options' => []],
                    'state' => ['label' => 'State/Province', 'type' => 'select', 'options' => []],
                    'city' => ['label' => 'City', 'type' => 'select', 'options' => []],
                    'home_town' => ['label' => 'Home Town', 'type' => 'text'],
                    'home_location' => ['label' => 'Home Location', 'type' => 'text'],
                    'origin_country' => ['label' => 'Origin Country', 'type' => 'text'],
                    'origin_state' => ['label' => 'Origin State', 'type' => 'text'],
                    'origin_city' => ['label' => 'Origin City', 'type' => 'text'],
                ],
            ],
            'Home & Lifestyle' => [
                'icon' => 'home',
                'fields' => [
                    'home_status' => [
                        'label' => 'Home Status',
                        'type' => 'select',
                        'options' => [
                            'Own' => 'Own',
                            'Rent' => 'Rent',
                            'Sharing' => 'Sharing',
                            'own' => 'Own',
                            'rented' => 'Rented',
                            'sharing' => 'Sharing',
                        ],
                    ],
                    'home_size' => ['label' => 'Home Size', 'type' => 'text'],
                    'other_properties' => ['label' => 'Other Properties', 'type' => 'textarea', 'placeholder' => 'List your other properties...'],
                    'weight_kg' => ['label' => 'Weight (KG)', 'type' => 'text', 'placeholder' => 'e.g. 70'],
                    'looking_to_marry' => ['label' => 'Looking To Marry', 'type' => 'select', 'options' => \App\Helpers\FormOptions::lookingToMarryOptions()],
                    'willing_to_relocate' => ['label' => 'Willing To Relocate', 'type' => 'select', 'options' => \App\Helpers\FormOptions::relocationOptions()],
                ],
            ],
            'Partner Preferences' => [
                'icon' => 'users',
                'fields' => [
                    'desired_age_min' => ['label' => 'Desired Age Min', 'type' => 'number', 'source' => 'preferences'],
                    'desired_age_max' => ['label' => 'Desired Age Max', 'type' => 'number', 'source' => 'preferences'],
                    'desired_height_min' => ['label' => 'Desired Height Min (cm)', 'type' => 'number', 'source' => 'preferences'],
                    'desired_height_max' => ['label' => 'Desired Height Max (cm)', 'type' => 'number', 'source' => 'preferences'],
                    'desired_religion' => ['label' => 'Desired Religion', 'type' => 'select', 'options' => \App\Helpers\FormOptions::religions(), 'source' => 'preferences'],
                    'desired_sect' => ['label' => 'Desired Sect', 'type' => 'select', 'options' => \App\Helpers\FormOptions::sects(), 'source' => 'preferences'],
                    'desired_sects' => ['label' => 'Desired Sects', 'type' => 'textarea', 'placeholder' => 'Comma-separated values', 'source' => 'preferences'],
                    'desired_caste' => ['label' => 'Desired Caste', 'type' => 'select', 'options' => \App\Helpers\FormOptions::castes(), 'source' => 'preferences'],
                    'desired_marital_status' => ['label' => 'Desired Marital Status', 'type' => 'select', 'options' => \App\Helpers\FormOptions::maritalStatuses(), 'source' => 'preferences'],
                    'desired_marital_statuses' => ['label' => 'Desired Marital Statuses', 'type' => 'textarea', 'placeholder' => 'Comma-separated values', 'source' => 'preferences'],
                    'desired_mother_tongue' => ['label' => 'Desired Mother Tongue', 'type' => 'select', 'options' => \App\Helpers\FormOptions::motherTongues(), 'source' => 'preferences'],
                    'desired_country' => ['label' => 'Desired Country', 'type' => 'text', 'source' => 'preferences'],
                    'desired_state' => ['label' => 'Desired State', 'type' => 'text', 'source' => 'preferences'],
                    'desired_city' => ['label' => 'Desired City', 'type' => 'text', 'source' => 'preferences'],
                    'desired_countries' => ['label' => 'Desired Countries', 'type' => 'textarea', 'placeholder' => 'Comma-separated values', 'source' => 'preferences'],
                    'desired_cities' => ['label' => 'Desired Cities', 'type' => 'textarea', 'placeholder' => 'Comma-separated values', 'source' => 'preferences'],
                    'desired_education' => ['label' => 'Desired Education', 'type' => 'textarea', 'placeholder' => 'Comma-separated values', 'source' => 'preferences'],
                    'desired_profession' => ['label' => 'Desired Profession', 'type' => 'select', 'options' => \App\Helpers\FormOptions::professions(), 'source' => 'preferences'],
                    'desired_professions' => ['label' => 'Desired Professions', 'type' => 'textarea', 'placeholder' => 'Comma-separated values', 'source' => 'preferences'],
                    'desired_religiousness' => ['label' => 'Desired Religiousness', 'type' => 'select', 'options' => \App\Helpers\FormOptions::religiousnessLevels(), 'source' => 'preferences'],
                    'desired_willing_to_relocate' => ['label' => 'Desired Relocation', 'type' => 'select', 'options' => \App\Helpers\FormOptions::relocationOptions(), 'source' => 'preferences'],
                    'desired_living_arrangement' => ['label' => 'Desired Living Arrangement', 'type' => 'select', 'options' => \App\Helpers\FormOptions::livingArrangements(), 'source' => 'preferences'],
                    'desired_income' => ['label' => 'Desired Income', 'type' => 'select', 'options' => \App\Helpers\FormOptions::incomeOptions(), 'source' => 'preferences'],
                    'desired_disability' => ['label' => 'Desired Disability', 'type' => 'select', 'options' => \App\Helpers\FormOptions::disabilityOptions(), 'source' => 'preferences'],
                    'desired_complexion' => ['label' => 'Desired Complexion', 'type' => 'select', 'options' => \App\Helpers\FormOptions::complexions(), 'source' => 'preferences'],
                    'desired_body_type' => ['label' => 'Desired Body Type', 'type' => 'select', 'options' => \App\Helpers\FormOptions::bodyTypes(), 'source' => 'preferences'],
                    'desired_family_values' => ['label' => 'Desired Family Values', 'type' => 'select', 'options' => \App\Helpers\FormOptions::familyValues(), 'source' => 'preferences'],
                    'desired_eating_habits' => ['label' => 'Desired Eating Habits', 'type' => 'select', 'options' => \App\Helpers\FormOptions::eatingHabits(), 'source' => 'preferences'],
                    'desired_smoking_habits' => ['label' => 'Desired Smoking Habits', 'type' => 'select', 'options' => \App\Helpers\FormOptions::smokingHabits(), 'source' => 'preferences'],
                    'other_requirements' => ['label' => 'Other Requirements', 'type' => 'textarea', 'source' => 'preferences'],
                ],
            ],
        ];

        return view('profile.wizard.complete', compact('profile', 'sections'));
    }

    public function storeCompleteProfile(Request $request, ProfileCompletionService $completionService, ImageService $imageService)
    {
        $profile = $this->getOrCreateProfile();

        $rules = [
            // Account
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore((int) auth()->id()),
            ],
            'hide_contact' => 'nullable|boolean',

            // Personal
            'gender' => 'nullable|string|in:male,female',
            'profile_for' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'marital_status' => 'nullable|string|max:255',
            'height_cm' => 'nullable|integer|min:120|max:250',
            'disability' => 'nullable|string|max:255',
            'mother_tongue' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'complexion' => 'nullable|string|max:50',
            'body_type' => 'nullable|string|max:50',
            'eating_habits' => 'nullable|string|max:50',
            'smoking_habits' => 'nullable|string|max:50',
            'drinking_habits' => 'nullable|string|max:50',
            'hobbies' => 'nullable|string|max:1000',

            // Identity
            'religion' => 'nullable|string|max:255',
            'sect' => 'nullable|string|max:255',
            'caste' => 'nullable|string|max:255',

            // Education
            'education' => 'nullable|string|max:255',
            'major_course' => 'nullable|string|max:255',
            'college' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',

            // Job
            'profession' => 'nullable|string|max:255',
            'job_type' => 'nullable|string|max:255',
            'nature_of_job' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'income' => 'nullable|string|max:255',

            // Family
            'father_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'brothers_count' => 'nullable|integer|min:0',
            'married_brothers_count' => 'nullable|integer|min:0',
            'sisters_count' => 'nullable|integer|min:0',
            'married_sisters_count' => 'nullable|integer|min:0',
            'married_siblings_count' => 'nullable|integer|min:0',
            'family_values' => 'nullable|string|max:50',
            'family_type' => 'nullable|string|max:50',
            'family_status' => 'nullable|string|max:50',
            'family_details' => 'nullable|string|max:2000',

            // Address
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'home_town' => 'nullable|string|max:255',
            'home_location' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',

            // Property
            'home_size' => 'nullable|string|max:255',
            'other_properties' => 'nullable|string|max:500',
            'home_status' => 'nullable|string|max:255',
            'weight_kg' => 'nullable|string|max:50',
            'religiousness' => 'nullable|string|max:255',
            'is_revert' => 'nullable',
            'origin_country' => 'nullable|string|max:255',
            'origin_state' => 'nullable|string|max:255',
            'origin_city' => 'nullable|string|max:255',
            'willing_to_relocate' => 'nullable|string|max:255',
            'looking_to_marry' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:10240',

            // Preferences
            'desired_age_min' => 'nullable|integer|min:18|max:70',
            'desired_age_max' => 'nullable|integer|min:18|max:80',
            'desired_height_min' => 'nullable|integer|min:100|max:220',
            'desired_height_max' => 'nullable|integer|min:100|max:220',
            'desired_religion' => 'nullable|string|max:255',
            'desired_sect' => 'nullable|string|max:255',
            'desired_sects' => 'nullable|string|max:2000',
            'desired_caste' => 'nullable|string|max:255',
            'desired_marital_status' => 'nullable|string|max:255',
            'desired_marital_statuses' => 'nullable|string|max:2000',
            'desired_mother_tongue' => 'nullable|string|max:255',
            'desired_country' => 'nullable|string|max:255',
            'desired_state' => 'nullable|string|max:255',
            'desired_city' => 'nullable|string|max:255',
            'desired_countries' => 'nullable|string|max:2000',
            'desired_cities' => 'nullable|string|max:2000',
            'desired_education' => 'nullable|string|max:2000',
            'desired_profession' => 'nullable|string|max:255',
            'desired_professions' => 'nullable|string|max:2000',
            'desired_religiousness' => 'nullable|string|max:255',
            'desired_willing_to_relocate' => 'nullable|string|max:255',
            'desired_living_arrangement' => 'nullable|string|max:255',
            'desired_income' => 'nullable|string|max:255',
            'desired_disability' => 'nullable|string|max:255',
            'desired_complexion' => 'nullable|string|max:255',
            'desired_body_type' => 'nullable|string|max:255',
            'desired_family_values' => 'nullable|string|max:255',
            'desired_eating_habits' => 'nullable|string|max:255',
            'desired_smoking_habits' => 'nullable|string|max:255',
            'other_requirements' => 'nullable|string|max:3000',
        ];

        // Only validate fields that were present in the request
        $dynamicRules = array_intersect_key($rules, $request->all());
        $validated = $request->validate($dynamicRules);

        $userPayload = array_intersect_key($validated, array_flip(['name', 'email']));
        $userPayload = array_filter($userPayload, fn ($value) => $value !== null && $value !== '');
        if ($userPayload !== []) {
            $user = auth()->user();
            if ($user) {
                $user->fill($userPayload);
                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                }
                $user->save();
            }
        }

        if (array_key_exists('brothers_count', $validated) || array_key_exists('sisters_count', $validated) || array_key_exists('married_brothers_count', $validated) || array_key_exists('married_sisters_count', $validated) || array_key_exists('married_siblings_count', $validated)) {
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
        }

        $profilePayload = \Illuminate\Support\Arr::except($validated, ['photo']);
        $profileColumns = array_flip(Schema::getColumnListing('profiles'));
        $profilePayload = array_intersect_key($profilePayload, $profileColumns);

        if (array_key_exists('hide_contact', $validated)) {
            $privacy = (array) ($profile->privacy_json ?? []);
            $privacy['hide_contact'] = (bool) $validated['hide_contact'];
            $profilePayload['privacy_json'] = $privacy;
        }

        $profile->update($profilePayload);

        $preferenceColumns = array_flip(array_diff((new Preference())->getFillable(), ['profile_id']));
        $preferencePayload = array_intersect_key($validated, $preferenceColumns);

        if ($preferencePayload !== []) {
            $arrayPreferenceKeys = [
                'desired_countries',
                'desired_cities',
                'desired_sects',
                'desired_education',
                'desired_professions',
                'desired_marital_statuses',
            ];

            foreach ($arrayPreferenceKeys as $arrayKey) {
                if (array_key_exists($arrayKey, $preferencePayload)) {
                    $preferencePayload[$arrayKey] = $this->parseDelimitedValues($preferencePayload[$arrayKey]);
                }
            }

            foreach ($preferencePayload as $key => $value) {
                if (is_string($value)) {
                    $trimmed = trim($value);
                    $preferencePayload[$key] = $trimmed === '' ? null : $trimmed;
                }
            }

            $preferences = $profile->preferences ?? new Preference(['profile_id' => $profile->id]);
            $preferences->fill($preferencePayload);
            $preferences->save();
        }

        if ($request->hasFile('photo')) {
            $this->storePhoto($profile, $request->file('photo'), $imageService);
        }

        $this->updateCompletionScore($profile, $completionService);

        if ($request->has('save_photo')) {
            return redirect()->back()->with('status', 'Profile photo updated successfully!');
        }

        return redirect()->route('dashboard')->with('status', 'Profile updated successfully!');
    }

    /**
     * @return array<int, string>
     */
    private function parseDelimitedValues($value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => trim((string) $item))
                ->filter(fn ($item) => $item !== '')
                ->unique()
                ->values()
                ->all();
        }

        $string = trim((string) $value);
        if ($string === '') {
            return [];
        }

        return collect(preg_split('/[\r\n,]+/', $string) ?: [])
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '')
            ->unique()
            ->values()
            ->all();
    }

    public function storePreferences(Request $request, ProfileCompletionService $completionService)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'desired_age_min' => 'nullable|integer|min:18|max:70',
            'desired_age_max' => 'nullable|integer|min:18|max:80',
            'desired_height_min' => 'nullable|integer|min:100|max:220',
            'desired_height_max' => 'nullable|integer|min:100|max:220',
            'desired_marital_status' => 'nullable|string',
            'desired_religion' => 'nullable|string',
            'desired_sect' => 'nullable|string',
            'desired_education' => 'nullable|string',
            'desired_country' => 'nullable|string',
            'desired_state' => 'nullable|string',
            'desired_city' => 'nullable|string',
            'desired_profession' => 'nullable|string',
            'desired_mother_tongue' => 'nullable|string',
            'desired_religiousness' => 'nullable|string',
            'desired_willing_to_relocate' => 'nullable|string',
            'desired_living_arrangement' => 'nullable|string',
            'desired_income' => 'nullable|string',
            'desired_disability' => 'nullable|string',
            'additional_preferences' => 'nullable|string|max:500',
        ]);

        $profile = $this->getOrCreateProfile();

        // Update bio on profile
        if (isset($validated['bio'])) {
            $profile->bio = $validated['bio'];
            $profile->save();
        }

        // Get or create preferences
        $preferences = $profile->preferences ?? new \App\Models\Preference(['profile_id' => $profile->id]);
        $preferences->fill($validated);
        $preferences->save();

        $this->updateCompletionScore($profile, $completionService);

        return redirect()->route('packages.index')->with('status', 'Profile setup complete! Choose a package to get started.');
    }

    public function showMobileUpload(Request $request, \App\Models\User $user, string $token)
    {
        $cachedToken = (string) Cache::get('mobile_upload_token_' . $user->id, '');
        $providedToken = (string) $token;

        if ($providedToken === '' || $cachedToken === '' || !hash_equals($cachedToken, $providedToken)) {
            \Illuminate\Support\Facades\Log::warning('Mobile upload token mismatch for user id ' . $user->id);
            abort(403, 'Invalid or expired token. Please refresh the verification page on your computer and scan the QR code again.');
        }

        $type = $this->normalizeMobileUploadType($request->query('type', 'photo'));
        return view('profile.wizard.mobile-upload', compact('user', 'type', 'token'));
    }

    public function storeMobileUpload(Request $request, \App\Models\User $user, string $token, ImageService $imageService)
    {
        $request->validate([
            'photo' => 'required|image|max:10240',
            'type' => 'nullable|string|in:' . implode(',', self::MOBILE_UPLOAD_TYPES),
        ]);

        $cachedToken = (string) Cache::get('mobile_upload_token_' . $user->id, '');
        $providedToken = (string) $token;

        if ($providedToken === '' || $cachedToken === '' || !hash_equals($cachedToken, $providedToken)) {
            \Illuminate\Support\Facades\Log::warning('Mobile upload token mismatch for user id ' . $user->id);
            abort(403, 'Invalid or expired token. Please refresh the verification page on your computer and scan the QR code again.');
        }

        $type = $this->normalizeMobileUploadType($request->input('type', 'photo'));
        $profile = $user->profile;

        if (!$profile) {
            return back()->with('error', 'Profile not found.');
        }

        if ($type === 'gallery' && $profile->photos()->where('is_primary', false)->count() >= self::GALLERY_LIMIT) {
            return response()->json([
                'message' => 'Gallery limit reached. You can upload up to ' . self::GALLERY_LIMIT . ' gallery photos.',
            ], 422);
        }

        $verificationUpload = $this->handleMobilePhotoUpload($profile, $type, $request, $imageService);

        if ($verificationUpload) {
            $profile->verification_status = 'pending';
            $profile->rejection_reason = null;
            $profile->save();
        }

        return redirect()->back()->with('status', 'Photo uploaded successfully! You can now close this window.');
    }

    public function checkPhotoStatus(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['uploaded' => false], 401);
        }

        $profile = $user->profile;
        if (!$profile) {
            return response()->json(['uploaded' => false]);
        }

        $type = $request->query('type', 'photo');
        
        if ($type === 'selfie') {
            if ($profile->selfie) {
                return response()->json([
                    'uploaded' => true, 
                    'photo_url' => \Illuminate\Support\Facades\Storage::url($profile->selfie)
                ]);
            }
        } elseif ($type === 'cnic_front') {
            if ($profile->cnic_front) {
                return response()->json([
                    'uploaded' => true,
                    'photo_url' => \Illuminate\Support\Facades\Storage::url($profile->cnic_front),
                ]);
            }
        } elseif ($type === 'cnic_back') {
            if ($profile->cnic_back) {
                return response()->json([
                    'uploaded' => true,
                    'photo_url' => \Illuminate\Support\Facades\Storage::url($profile->cnic_back),
                ]);
            }
        } else {
            // Default check for profile photos
            if ($profile->photos()->exists()) {
                return response()->json([
                    'uploaded' => true, 
                    'photo_url' => $profile->primary_photo_url
                ]);
            }
        }

        return response()->json(['uploaded' => false])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }

    public function deletePhoto(Request $request, ImageService $imageService)
    {
        $user = auth()->user();
        $profile = $user->profile;
        $type = $request->input('type', 'photo');

        if (!$profile) {
            return response()->json(['success' => false], 404);
        }

        if ($type === 'selfie') {
            if ($profile->selfie) {
                $imageService->delete($profile->selfie);
                $profile->selfie = null;
                $profile->save();
            }
        } 
        // Add logic for other types if needed (e.g., CNIC)

        return response()->json(['success' => true]);
    }
}
