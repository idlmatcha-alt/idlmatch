<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
        'first_name',
        'last_name',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'admin_last_seen_users_at' => 'datetime',
        'admin_last_seen_subscriptions_at' => 'datetime',
        'admin_last_seen_blog_posts_at' => 'datetime',
        'admin_last_seen_reports_at' => 'datetime',
        'admin_last_seen_verifications_at' => 'datetime',
        'admin_last_seen_success_stories_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function successStories()
    {
        return $this->hasMany(SuccessStory::class, 'submitted_by');
    }

    public function isAdmin()
    {
        return strtolower(trim((string) $this->role)) === 'admin';
    }

    public function scopeNonAdmin($query)
    {
        return $query->whereRaw("LOWER(TRIM(COALESCE(role, ''))) != ?", ['admin']);
    }

    /**
     * Memoized active subscription — avoids repeated DB queries
     * when called from canViewProfile(), canViewContacts(), canMessage(), etc.
     */
    protected $_cachedActiveSubscription = null;
    protected $_activeSubResolved = false;

    public function getActiveSubscription()
    {
        if ($this->relationLoaded('activeSubscription')) {
            return $this->getRelation('activeSubscription');
        }

        if (!$this->_activeSubResolved) {
            $this->_cachedActiveSubscription = $this->subscriptions()
                ->where('status', 'active')
                ->where('end_at', '>', now())
                ->with('plan')
                ->first();
            $this->_activeSubResolved = true;
        }

        return $this->_cachedActiveSubscription;
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->latest('end_at');
    }

    public function profileViews()
    {
        return $this->hasMany(ProfileView::class, 'viewer_id');
    }

    public function getProfileViewLimit(): int
    {
        $sub = $this->getActiveSubscription();
        if (!$sub)
            return 0; // Default if no sub? Or maybe Free?

        return (int) ($sub->plan->limits_json['profile_views'] ?? 0);
    }

    public function getUsedProfileViewsCount(): int
    {
        $sub = $this->getActiveSubscription();
        if (!$sub)
            return 0;

        return $this->profileViews()
            ->where('created_at', '>=', $sub->created_at)
            ->count();
    }

    public function canViewProfile(Profile $profile): bool
    {
        // Own profile
        if ($this->profile && $this->profile->id === $profile->id)
            return true;

        // Already viewed
        if ($this->profileViews()->where('viewed_id', $profile->id)->exists())
            return true;

        // Within limit
        return $this->getUsedProfileViewsCount() < $this->getProfileViewLimit();
    }

    public function recordProfileView(Profile $profile)
    {
        if ($this->profile && $this->profile->id === $profile->id)
            return;

        ProfileView::firstOrCreate([
            'viewer_id' => $this->id,
            'viewed_id' => $profile->id,
        ]);
    }

    public function canViewContacts(): bool
    {
        $sub = $this->getActiveSubscription();
        if (!$sub)
            return false;

        return (float) ($sub->plan->price ?? 0) > 0;
    }

    public function isVerifiedMember(): bool
    {
        $sub = $this->getActiveSubscription();
        if (!$sub)
            return false;

        return (bool) ($sub->plan->limits_json['verified_badge'] ?? false);
    }

    public function canMessage(): bool
    {
        $sub = $this->getActiveSubscription();
        if (!$sub) {
            return false;
        }

        return (float) ($sub->plan->price ?? 0) > 0;
    }

    public function canAccessFilament(): bool
    {
        return $this->isAdmin();
    }

    public function getFirstNameAttribute()
    {
        return explode(' ', $this->name, 2)[0] ?? '';
    }

    public function getLastNameAttribute()
    {
        return explode(' ', $this->name, 2)[1] ?? '';
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['name'] = trim($value . ' ' . $this->last_name);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['name'] = trim($this->first_name . ' ' . $value);
    }

    public function unreadMessagesCount(): int
    {
        if (!$this->profile)
            return 0;

        return \Illuminate\Support\Facades\Cache::remember("user_{$this->id}_unread_msgs", 60, function () {
            return Message::whereHas('thread', function ($query) {
                $query->where('profile_a_id', $this->profile->id)
                    ->orWhere('profile_b_id', $this->profile->id);
            })
                ->where('sender_profile_id', '!=', $this->profile->id)
                ->whereNull('read_at')
                ->count();
        });
    }

    public function unreadInterestsCount(): int
    {
        if (!$this->profile)
            return 0;

        return \Illuminate\Support\Facades\Cache::remember("user_{$this->id}_unread_interests", 60, function () {
            return Interest::where('to_profile_id', $this->profile->id)
                ->whereNull('read_at')
                ->count();
        });
    }
}
