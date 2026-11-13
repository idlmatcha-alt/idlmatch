<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'display_id',
        'profile_for',
        'gender',
        'dob',
        'height_cm',
        'weight_kg',
        'disability',
        'religiousness',
        'is_revert',
        'sect',
        'religion',
        'caste',
        'mother_tongue',
        'marital_status',
        'education',
        'college',
        'university',
        'profession',
        'job_type',
        'income',
        'nature_of_job',
        'company_name',
        'phone',
        'mobile_no',
        'family_details',
        'father_name',
        'father_occupation',
        'mother_occupation',
        'brothers_count',
        'married_brothers_count',
        'sisters_count',
        'married_sisters_count',
        'married_siblings_count',
        'bio',
        'country',
        'city',
        'home_town',
        'home_status',
        'home_size',
        'home_location',
        'other_properties',
        'state',
        'origin_country',
        'origin_state',
        'origin_city',
        'willing_to_relocate',
        'looking_to_marry',
        'complexion',
        'body_type',
        'eating_habits',
        'smoking_habits',
        'drinking_habits',
        'hobbies',
        'family_values',
        'family_type',
        'family_status',
        'privacy_json',
        'completion_score',
        'last_active_at',
        'status',
        'major_course',
        'cnic_front',
        'cnic_back',
        'selfie',
        'cnic_verified',
        'cnic_verified_at',
        'verification_status',
        'rejection_reason',
    ];

    protected $casts = [
        'dob' => 'date',
        'privacy_json' => 'array',
        'last_active_at' => 'datetime',
        'is_revert' => 'boolean',
    ];

    /**
     * Generate a unique display ID in format: 2 letters + 4 digits (e.g., AB1234)
     * This helps with record matching and is more user-friendly
     */
    public static function generateUniqueDisplayId(): string
    {
        do {
            // Generate 2 random uppercase letters
            $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2));
            
            // Generate 4 random digits
            $digits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            
            $displayId = $letters . $digits;
            
            // Check if this ID already exists
            $exists = self::where('display_id', $displayId)->exists();
        } while ($exists);
        
        return $displayId;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preferences()
    {
        return $this->hasOne(Preference::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function primaryPhoto()
    {
        return $this->hasOne(Photo::class)->where('is_primary', true)->where('status', 'approved');
    }

    public function getPrimaryPhotoUrlAttribute()
    {
        $primary = $this->photos()->where('is_primary', true)->first();
        if ($primary) {
            return Storage::url($primary->path);
        }
        
        $first = $this->photos()->first();
        if ($first) {
            return Storage::url($first->path);
        }

        return null;
    }

    public function interestsSent()
    {
        return $this->hasMany(Interest::class, 'from_profile_id');
    }

    public function interestsReceived()
    {
        return $this->hasMany(Interest::class, 'to_profile_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'blocker_profile_id');
    }

    public function threadsAsA()
    {
        return $this->hasMany(Thread::class, 'profile_a_id');
    }

    public function threadsAsB()
    {
        return $this->hasMany(Thread::class, 'profile_b_id');
    }

    public function getAgeAttribute()
    {
        if (!$this->dob) {
            return null;
        }

        return $this->dob->age;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * User-side visibility: active profiles owned by non-admin users.
     */
    public function scopeVisibleToUsers($query)
    {
        return $query
            ->where('status', 'active')
            ->whereHas('user', function ($q) {
                $q->nonAdmin();
            });
    }

    public function getFirstNameAttribute()
    {
        return $this->user->first_name ?? '';
    }

    public function getLastNameAttribute()
    {
        return $this->user->last_name ?? '';
    }

    /**
     * Calculate match percentage between this profile and a target profile's preferences.
     * Logic: How well does THIS profile fit the TARGET profile's requirements.
     */
    public function calculateMatchPercentage(?Profile $targetProfile): int
    {
        if (!$targetProfile || !$targetProfile->preferences) {
            return 0;
        }

        $prefs = $targetProfile->preferences;
        $score = 0;
        $totalWeight = 10;

        // 1. Religion (Weight: 2.5) - CRITICAL
        // If religion is specified and doesn't match, we might want to penalize heavily or just give 0 points for this section.
        if (!empty($prefs->desired_religion)) {
            if ($this->religion === $prefs->desired_religion) {
                $score += 2.5;
            }
        } else {
            // If no preference, full points (neutral)
            $score += 2.5;
        }

        // 2. Age (Weight: 1.5)
        if ($this->age >= $prefs->desired_age_min && $this->age <= $prefs->desired_age_max) {
            $score += 1.5;
        }

        // 3. Marital Status (Weight: 1.5)
        $matchesStatus = false;
        if (!empty($prefs->desired_marital_status)) {
            $matchesStatus = ($this->marital_status === $prefs->desired_marital_status);
        } else {
            $desiredMaritalStatuses = is_array($prefs->desired_marital_statuses) ? $prefs->desired_marital_statuses : [];
            if (empty($desiredMaritalStatuses) || in_array($this->marital_status, $desiredMaritalStatuses)) {
                $matchesStatus = true;
            }
        }
        if ($matchesStatus) {
            $score += 1.5;
        }

        // 4. Sect (Weight: 1.0)
        $matchesSect = false;
        if (!empty($prefs->desired_sect)) {
            $matchesSect = ($this->sect === $prefs->desired_sect);
        } else {
            $desiredSects = is_array($prefs->desired_sects) ? $prefs->desired_sects : [];
            if (empty($desiredSects) || in_array($this->sect, $desiredSects)) {
                $matchesSect = true;
            }
        }
        if ($matchesSect) {
            $score += 1.0;
        }

        // 5. Mother Tongue (Weight: 1.0)
        $matchesTongue = false;
        if (!empty($prefs->desired_mother_tongue)) {
             $matchesTongue = ($this->mother_tongue === $prefs->desired_mother_tongue);
        } else {
             $desiredTongues = is_array($prefs->desired_mother_tongues) ? $prefs->desired_mother_tongues : [];
             if (empty($desiredTongues) || in_array($this->mother_tongue, $desiredTongues)) {
                 $matchesTongue = true;
             }
        }
        if ($matchesTongue) {
            $score += 1.0;
        }

        // 6. Education (Weight: 1.0)
        $matchesEducation = false;
        $prefEducation = $prefs->desired_education;
        if (empty($prefEducation)) {
            $matchesEducation = true;
        } elseif (is_string($prefEducation)) {
            $decoded = json_decode($prefEducation, true);
            if (is_array($decoded)) {
                $matchesEducation = in_array($this->education, $decoded);
            } else {
                $matchesEducation = ($this->education === $prefEducation);
            }
        } elseif (is_array($prefEducation)) {
            $matchesEducation = in_array($this->education, $prefEducation);
        }
        if ($matchesEducation) {
            $score += 1.0;
        }

        // 7. City (Weight: 0.5)
        $matchesCity = false;
        if (!empty($prefs->desired_city)) {
            $matchesCity = ($this->city === $prefs->desired_city);
        } else {
            $desiredCities = is_array($prefs->desired_cities) ? $prefs->desired_cities : [];
            if (empty($desiredCities) || in_array($this->city, $desiredCities)) {
                $matchesCity = true;
            }
        }
        if ($matchesCity) {
            $score += 0.5;
        }

        // 8. Height (Weight: 0.5)
        if ($this->height_cm >= $prefs->desired_height_min && $this->height_cm <= $prefs->desired_height_max) {
            $score += 0.5;
        }

        // 9. Profession (Weight: 0.5)
        $desiredProfessions = is_array($prefs->desired_professions) ? $prefs->desired_professions : [];
        // Handle string preference as well
        if (!empty($prefs->desired_profession) && empty($desiredProfessions)) {
             if ($this->profession === $prefs->desired_profession) {
                 $score += 0.5;
             }
        } elseif (empty($desiredProfessions) || in_array($this->profession, $desiredProfessions)) {
            $score += 0.5;
        }

        // 10. Caste (Weight: 0.5) - Kept mostly for cultural relevance
        if (empty($prefs->desired_caste) || $this->caste === $prefs->desired_caste) {
            $score += 0.0; // Reduced to 0 for now to keep total 10, or we can make total 10.5
            // Actually, let's keep it simple. Total is 10.
            // Current Score so far: 2.5 + 1.5 + 1.5 + 1.0 + 1.0 + 1.0 + 0.5 + 0.5 + 0.5 = 10.0
            // So Caste is bonus? Or maybe just ignored for score but shown in UI.
            // Let's add 0.5 bonus capped at 100%?
        }

        $percentage = ($score / $totalWeight) * 100;
        return (int) min(100, $percentage);
    }

    /**
     * Check if this profile is currently online (active within last 5 minutes).
     */
    public function isOnline(): bool
    {
        if (!$this->last_active_at) {
            return false;
        }

        $now = now();
        return $this->last_active_at->lte($now) && $this->last_active_at->gt($now->copy()->subMinutes(5));
    }

    /**
     * Get human-readable "last seen" text
     */
    public function getLastSeenText(): string
    {
        if ($this->isOnline()) {
            return 'Online now';
        }

        if (!$this->last_active_at) {
            return 'Last seen unknown';
        }

        $now = now();
        $lastActiveAt = $this->last_active_at;

        if ($lastActiveAt->isFuture()) {
            if ($lastActiveAt->isTomorrow()) {
                return 'Last seen tomorrow';
            }

            return 'Last seen ' . $lastActiveAt->diffForHumans($now);
        }

        if ($lastActiveAt->isToday()) {
            return 'Last seen today, ' . $lastActiveAt->diffForHumans($now, true) . ' ago';
        }

        if ($lastActiveAt->isYesterday()) {
            return 'Last seen yesterday';
        }

        $daysAgo = $lastActiveAt->diffInDays($now);
        if ($daysAgo <= 7) {
            return 'Last seen ' . $daysAgo . ' day' . ($daysAgo === 1 ? '' : 's') . ' ago';
        }

        return 'Last seen on ' . $lastActiveAt->format('d M Y');
    }
}
