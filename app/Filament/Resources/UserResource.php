<?php

namespace App\Filament\Resources;

use App\Helpers\FormOptions;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Profile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'All Users';

    protected static ?string $navigationGroup = 'Users Management';

    protected static ?int $navigationSort = 1;

    /** Makes members findable from the search box at the top of the panel. */
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return ['Email' => $record->email];
    }

    /** @var array<string, array<int, string>> */
    private static array $tableColumnsCache = [];

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['profile.photos', 'profile.primaryPhoto', 'profile.preferences']);
    }

    public static function getNavigationBadge(): ?string
    {
        $admin = auth()->user();
        if (!$admin) {
            return null;
        }

        $query = static::getModel()::query()
            ->nonAdmin();

        if ($admin->admin_last_seen_users_at) {
            $query->where('created_at', '>', $admin->admin_last_seen_users_at);
        }

        $count = $query->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    /**
     * The approved reference sidebar splits the member list into three saved
     * views. They are all the same index route with one table filter
     * pre-applied, so there is only one user table to keep in sync.
     *
     * @return array<int, NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        $routeBaseName = static::getRouteBaseName();

        $views = [
            ['All Users', 'heroicon-o-users', 1, []],
            ['Verified Users', 'heroicon-o-badge-check', 2, ['verification' => 'verified']],
            ['Blocked Users', 'heroicon-o-ban', 3, ['status' => 'banned']],
        ];

        $items = [];

        foreach ($views as [$label, $icon, $sort, $filters]) {
            $parameters = [];

            foreach ($filters as $name => $value) {
                $parameters['tableFilters'][$name]['value'] = $value;
            }

            $item = NavigationItem::make($label)
                ->group(static::getNavigationGroup())
                ->icon($icon)
                ->sort($sort)
                ->isActiveWhen(fn (): bool => request()->routeIs("{$routeBaseName}.*") && static::navigationFiltersMatch($filters))
                ->url(static::getUrl('index', $parameters));

            if ($filters === []) {
                $item->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor());
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Only one of the three member views may light up at a time, so the plain
     * "All Users" entry is active whenever no verification/status filter is set.
     *
     * @param  array<string, string>  $expected
     */
    protected static function navigationFiltersMatch(array $expected): bool
    {
        /** @var array<string, array<string, string>> $current */
        $current = (array) request()->query('tableFilters', []);

        if ($expected === []) {
            return ! isset($current['verification']) && ! isset($current['status']);
        }

        foreach ($expected as $name => $value) {
            if (($current[$name]['value'] ?? null) !== $value) {
                return false;
            }
        }

        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ========== ACCOUNT INFO ==========
                Forms\Components\Section::make('Account Information')
                    ->schema(static::applyVisibilityHints([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required(),
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\Select::make('role')
                            ->options([
                                'user' => 'User',
                                'admin' => 'Admin',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'banned' => 'Banned',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Registered On')
                            ->content(fn ($record) => $record?->created_at?->format('M d, Y h:i A') ?? '-'),
                    ], 'user'))->columns(3),

                Forms\Components\Section::make('Visibility Legend')
                    ->description('Meaning of visibility badges shown next to field labels.')
                    ->visible(fn ($livewire) => !($livewire instanceof Pages\CreateUser))
                    ->aside()
                    ->schema([
                        Forms\Components\Placeholder::make('visibility_legend_help')
                            ->label('')
                            ->content(new HtmlString('
                                <div style="display:flex; flex-direction:column; gap:8px; font-size:12px; color:#334155;">
                                    <div><span style="display:inline-block;padding:2px 8px;border-radius:999px;background:#dcfce7;color:#166534;font-weight:700;">Public</span> Visible on public profile page.</div>
                                    <div><span style="display:inline-block;padding:2px 8px;border-radius:999px;background:#fef3c7;color:#92400e;font-weight:700;">Restricted</span> Visible only to owner or paid/logged-in members.</div>
                                    <div><span style="display:inline-block;padding:2px 8px;border-radius:999px;background:#e2e8f0;color:#334155;font-weight:700;">Private</span> Not shown on public profile page.</div>
                                </div>
                            ')),
                    ]),

                // ========== EDITABLE FRONTEND DATA ==========
                Forms\Components\Section::make('Editable Frontend Profile Data')
                    ->description('Admin updates here are saved to the member profile and reflected on frontend pages.')
                    ->visible(fn ($livewire) => !($livewire instanceof Pages\CreateUser))
                    ->relationship('profile')
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        $data['display_id'] = $data['display_id'] ?? Profile::generateUniqueDisplayId();
                        $data['gender'] = $data['gender'] ?? 'male';
                        $data['dob'] = $data['dob'] ?? now()->subYears(25)->toDateString();
                        $data['status'] = $data['status'] ?? 'active';

                        return $data;
                    })
                    ->schema(static::applyVisibilityHints(static::filterFieldComponentsForTable([
                        Forms\Components\Select::make('gender')
                            ->options(['male' => 'Male', 'female' => 'Female']),
                        Forms\Components\DatePicker::make('dob')->label('Date of Birth'),
                        Forms\Components\TextInput::make('height_cm')->label('Height (cm)')->numeric(),
                        Forms\Components\Select::make('marital_status')
                            ->options(static::valueOptions(FormOptions::maritalStatuses())),

                        Forms\Components\Select::make('disability')
                            ->options(static::valueOptions(FormOptions::disabilityOptions())),
                        Forms\Components\Select::make('mother_tongue')
                            ->options(static::valueOptions(FormOptions::motherTongues())),
                        Forms\Components\Select::make('religion')
                            ->options(static::valueOptions(FormOptions::religions())),
                        Forms\Components\Select::make('sect')
                            ->options(static::valueOptions(FormOptions::sects())),

                        Forms\Components\Select::make('caste')
                            ->options(static::valueOptions(FormOptions::castes())),
                        Forms\Components\Select::make('education')
                            ->label('Qualification')
                            ->options(static::valueOptions(FormOptions::educationLevels())),
                        Forms\Components\Select::make('major_course')
                            ->options(static::flattenOptionValues(FormOptions::majorsByQualification())),
                        Forms\Components\TextInput::make('college'),

                        Forms\Components\TextInput::make('university'),
                        Forms\Components\Select::make('profession')
                            ->options(static::flattenOptionValues(FormOptions::professions())),
                        Forms\Components\TextInput::make('job_type')->label('Job Post'),
                        Forms\Components\TextInput::make('nature_of_job')->label('Nature of Job'),

                        Forms\Components\TextInput::make('company_name'),
                        Forms\Components\Select::make('income')
                            ->options(static::valueOptions(FormOptions::incomeOptions())),
                        Forms\Components\TextInput::make('father_name'),
                        Forms\Components\TextInput::make('father_occupation'),

                        Forms\Components\TextInput::make('mother_occupation'),
                        Forms\Components\TextInput::make('brothers_count')->numeric(),
                        Forms\Components\TextInput::make('married_brothers_count')->numeric(),
                        Forms\Components\TextInput::make('sisters_count')->numeric(),

                        Forms\Components\TextInput::make('married_sisters_count')->numeric(),
                        Forms\Components\TextInput::make('married_siblings_count')->numeric(),
                        Forms\Components\Select::make('family_values')
                            ->options(static::valueOptions(FormOptions::familyValues())),
                        Forms\Components\Select::make('family_type')
                            ->options(static::valueOptions(FormOptions::familyTypes())),

                        Forms\Components\Select::make('family_status')
                            ->options(static::valueOptions(FormOptions::familyStatuses())),
                        Forms\Components\TextInput::make('mobile_no'),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\TextInput::make('country'),

                        Forms\Components\TextInput::make('state'),
                        Forms\Components\Select::make('city')
                            ->options(static::valueOptions(FormOptions::cities())),
                        Forms\Components\TextInput::make('home_town'),
                        Forms\Components\TextInput::make('home_location'),

                        Forms\Components\Select::make('home_status')
                            ->options([
                                'Own' => 'Own',
                                'Rent' => 'Rent',
                                'Sharing' => 'Sharing',
                                'own' => 'own',
                                'rented' => 'rented',
                                'sharing' => 'sharing',
                            ]),
                        Forms\Components\TextInput::make('home_size'),
                        Forms\Components\TextInput::make('weight_kg'),
                        Forms\Components\Select::make('religiousness')
                            ->options(static::valueOptions(FormOptions::religiousnessLevels())),

                        Forms\Components\Select::make('willing_to_relocate')
                            ->options(static::valueOptions(FormOptions::relocationOptions())),
                        Forms\Components\Select::make('looking_to_marry')
                            ->options(static::valueOptions(FormOptions::lookingToMarryOptions())),
                        Forms\Components\Select::make('complexion')
                            ->options(static::valueOptions(FormOptions::complexions())),
                        Forms\Components\Select::make('body_type')
                            ->options(static::valueOptions(FormOptions::bodyTypes())),

                        Forms\Components\Select::make('eating_habits')
                            ->options(static::valueOptions(FormOptions::eatingHabits())),
                        Forms\Components\Select::make('smoking_habits')
                            ->options(static::valueOptions(FormOptions::smokingHabits())),
                        Forms\Components\Select::make('drinking_habits')
                            ->options(static::valueOptions(FormOptions::drinkingHabits())),
                        Forms\Components\TextInput::make('completion_score')->numeric()->disabled(),

                        Forms\Components\Textarea::make('hobbies')->columnSpanFull(),
                        Forms\Components\Textarea::make('bio')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('other_properties')->rows(3)->columnSpanFull(),
                    ], 'profiles'), 'profile'))
                    ->columns(4)
                    ->collapsible(),

                Forms\Components\Section::make('Editable Partner Preferences')
                    ->description('Admin can edit partner preference fields from here.')
                    ->visible(fn ($livewire) => !($livewire instanceof Pages\CreateUser))
                    ->relationship('profile')
                    ->schema([
                        Forms\Components\Section::make('Preference Fields')
                            ->relationship('preferences')
                            ->schema(static::applyVisibilityHints(static::filterFieldComponentsForTable([
                                Forms\Components\TextInput::make('desired_age_min')->numeric(),
                                Forms\Components\TextInput::make('desired_age_max')->numeric(),
                                Forms\Components\TextInput::make('desired_height_min')->numeric(),
                                Forms\Components\TextInput::make('desired_height_max')->numeric(),
                                Forms\Components\TextInput::make('desired_religion'),
                                Forms\Components\TextInput::make('desired_sect'),
                                Forms\Components\TagsInput::make('desired_sects'),
                                Forms\Components\TextInput::make('desired_caste'),
                                Forms\Components\TextInput::make('desired_marital_status'),
                                Forms\Components\TagsInput::make('desired_marital_statuses'),
                                Forms\Components\TextInput::make('desired_mother_tongue'),
                                Forms\Components\TextInput::make('desired_country'),
                                Forms\Components\TextInput::make('desired_state'),
                                Forms\Components\TextInput::make('desired_city'),
                                Forms\Components\TagsInput::make('desired_countries'),
                                Forms\Components\TagsInput::make('desired_cities'),
                                Forms\Components\TagsInput::make('desired_education'),
                                Forms\Components\TextInput::make('desired_profession'),
                                Forms\Components\TagsInput::make('desired_professions'),
                                Forms\Components\TextInput::make('desired_religiousness'),
                                Forms\Components\TextInput::make('desired_willing_to_relocate'),
                                Forms\Components\TextInput::make('desired_living_arrangement'),
                                Forms\Components\TextInput::make('desired_income'),
                                Forms\Components\TextInput::make('desired_disability'),
                                Forms\Components\TextInput::make('desired_complexion'),
                                Forms\Components\TextInput::make('desired_body_type'),
                                Forms\Components\TextInput::make('desired_family_values'),
                                Forms\Components\TextInput::make('desired_eating_habits'),
                                Forms\Components\TextInput::make('desired_smoking_habits'),
                                Forms\Components\Textarea::make('other_requirements')->columnSpanFull(),
                            ], 'preferences'), 'preferences'))
                            ->columns(4),
                    ])
                    ->collapsible(),

                // ========== PROFILE PHOTOS ==========
                Forms\Components\Section::make('Profile Photos')
                    ->schema([
                        Forms\Components\Placeholder::make('photos_gallery')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->profile) {
                                    return 'No profile found';
                                }
                                
                                $photos = $record->profile->photos ?? collect();
                                if ($photos->isEmpty()) {
                                    return 'No photos uploaded';
                                }
                                
                                $html = '<div style="display: flex; gap: 12px; flex-wrap: wrap;">';
                                foreach ($photos as $photo) {
                                    $url = asset('storage/' . ltrim($photo->path, '/'));
                                    $isPrimary = $photo->is_primary ? '<span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; position: absolute; top: 4px; left: 4px;">Primary</span>' : '';
                                    $html .= '<div style="position: relative; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">';
                                    $html .= $isPrimary;
                                    $html .= '<img src="' . $url . '" style="width: 120px; height: 120px; object-fit: cover;">';
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                
                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ])->collapsible(),

                // ========== SELFIE VERIFICATION ==========
                Forms\Components\Section::make('Selfie Verification')
                    ->schema([
                        Forms\Components\Placeholder::make('selfie_image')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->profile || !$record->profile->selfie) {
                                    return 'No selfie uploaded';
                                }
                                
                                $url = asset('storage/' . $record->profile->selfie);
                                return new \Illuminate\Support\HtmlString(
                                    '<div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; display: inline-block;">
                                        <img src="' . $url . '" style="max-width: 300px; height: auto;">
                                    </div>'
                                );
                            }),
                    ])->collapsible(),

                // ========== CNIC VERIFICATION ==========
                Forms\Components\Section::make('CNIC Documents')
                    ->schema([
                        Forms\Components\Placeholder::make('cnic_images')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->profile) {
                                    return 'No profile found';
                                }
                                
                                $profile = $record->profile;
                                $html = '<div style="display: flex; gap: 20px; flex-wrap: wrap;">';
                                
                                // CNIC Front
                                if ($profile && $profile->cnic_front) {
                                    $url = asset('storage/' . $profile->cnic_front);
                                    $html .= '<div style="text-align: center;">
                                        <div style="font-weight: 600; margin-bottom: 8px; color: #374151;">CNIC Front</div>
                                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                                            <img src="' . $url . '" style="max-width: 300px; height: auto;">
                                        </div>
                                    </div>';
                                } else {
                                    $html .= '<div style="text-align: center;">
                                        <div style="font-weight: 600; margin-bottom: 8px; color: #374151;">CNIC Front</div>
                                        <div style="padding: 40px; background: #f3f4f6; border-radius: 8px; color: #9ca3af;">Not uploaded</div>
                                    </div>';
                                }
                                
                                // CNIC Back
                                if ($profile && $profile->cnic_back) {
                                    $url = asset('storage/' . $profile->cnic_back);
                                    $html .= '<div style="text-align: center;">
                                        <div style="font-weight: 600; margin-bottom: 8px; color: #374151;">CNIC Back</div>
                                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                                            <img src="' . $url . '" style="max-width: 300px; height: auto;">
                                        </div>
                                    </div>';
                                } else {
                                    $html .= '<div style="text-align: center;">
                                        <div style="font-weight: 600; margin-bottom: 8px; color: #374151;">CNIC Back</div>
                                        <div style="padding: 40px; background: #f3f4f6; border-radius: 8px; color: #9ca3af;">Not uploaded</div>
                                    </div>';
                                }
                                
                                $html .= '</div>';
                                
                                // Verification status
                                $verifiedBadge = $profile->cnic_verified 
                                    ? '<div style="margin-top: 16px; padding: 8px 16px; background: #dcfce7; color: #166534; border-radius: 8px; display: inline-block;"><strong>✓ CNIC Verified</strong></div>'
                                    : '<div style="margin-top: 16px; padding: 8px 16px; background: #fef3c7; color: #92400e; border-radius: 8px; display: inline-block;"><strong>⏳ Not Verified</strong></div>';
                                $html .= $verifiedBadge;
                                
                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ])->collapsible(),

                // ========== SUBSCRIPTION INFO ==========
                Forms\Components\Section::make('Subscription')
                    ->schema([
                        Forms\Components\Placeholder::make('subscription_info')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record) return 'No data';
                                
                                $subscription = $record->getActiveSubscription();
                                if (!$subscription) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<span style="padding: 4px 12px; background: #f3f4f6; color: #6b7280; border-radius: 4px;">Free Plan</span>'
                                    );
                                }
                                
                                $planName = $subscription->plan->name ?? 'Unknown';
                                $expiresAt = $subscription->end_at?->format('M d, Y') ?? 'N/A';
                                
                                return new \Illuminate\Support\HtmlString(
                                    '<div style="display: flex; gap: 16px; align-items: center;">
                                        <span style="padding: 4px 12px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-weight: 600;">' . e($planName) . '</span>
                                        <span style="color: #6b7280;">Expires: ' . e($expiresAt) . '</span>
                                    </div>'
                                );
                            }),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'danger' => 'admin',
                        'primary' => 'user',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'secondary' => 'inactive',
                        'danger' => 'banned',
                    ]),
                Tables\Columns\TextColumn::make('profile.display_id')->label('Profile ID'),
                Tables\Columns\BadgeColumn::make('profile.verification_status')
                    ->label('Verification')
                    ->colors([
                        'success' => 'verified',
                        'warning' => 'pending',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'banned' => 'Banned',
                    ]),
                // Drives the "Verified Users" sidebar entry; the value lives on
                // the profile, so the filter reaches through the relation.
                // (attribute('profile.verification_status') is not used: this
                // Laravel/Filament pair fatals on whereRelation() for HasOne.)
                Tables\Filters\SelectFilter::make('verification')
                    ->label('Verification')
                    ->options([
                        'pending' => 'Pending review',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return $query;
                        }

                        return $query->whereHas(
                            'profile',
                            fn (Builder $profile): Builder => $profile->where('verification_status', $value),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * @return array{status: string, note: string}
     */
    private static function visibilityForField(string $modelType, string $field): array
    {
        $publicStatuses = [
            'public' => 'Publicly Available',
            'restricted' => 'Restricted',
            'private' => 'Private',
        ];

        $notes = [
            'public' => 'Shown on public profile page.',
            'restricted' => 'Shown only to profile owner or paid/logged-in members.',
            'private' => 'Not shown on public profile page.',
        ];

        $publicUserFields = ['first_name', 'last_name', 'name'];
        $restrictedUserFields = ['email'];

        $publicProfileFields = [
            'display_id',
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
            'university',
            'profession',
            'job_type',
            'nature_of_job',
            'income',
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
            'state',
            'city',
            'home_town',
            'home_status',
            'home_size',
            'other_properties',
            'origin_country',
            'origin_state',
            'origin_city',
            'willing_to_relocate',
            'looking_to_marry',
            'hobbies',
            'cnic_verified',
            'verification_status',
        ];
        $restrictedProfileFields = ['mobile_no'];

        $publicPreferenceFields = [
            'desired_age_min',
            'desired_age_max',
            'desired_height_min',
            'desired_height_max',
            'desired_country',
            'desired_state',
            'desired_countries',
            'desired_cities',
            'desired_mother_tongue',
            'desired_sects',
            'desired_religion',
            'desired_sect',
            'desired_education',
            'desired_profession',
            'desired_professions',
            'desired_marital_statuses',
            'desired_caste',
            'desired_marital_status',
            'desired_city',
            'desired_religiousness',
            'desired_willing_to_relocate',
            'desired_living_arrangement',
            'desired_income',
            'desired_disability',
        ];

        $statusKey = 'private';

        if ($modelType === 'user') {
            if (in_array($field, $publicUserFields, true)) {
                $statusKey = 'public';
            } elseif (in_array($field, $restrictedUserFields, true)) {
                $statusKey = 'restricted';
            }
        } elseif ($modelType === 'profile') {
            if (in_array($field, $publicProfileFields, true)) {
                $statusKey = 'public';
            } elseif (in_array($field, $restrictedProfileFields, true)) {
                $statusKey = 'restricted';
            }
        } elseif ($modelType === 'preferences') {
            if (in_array($field, $publicPreferenceFields, true)) {
                $statusKey = 'public';
            }
        }

        return [
            'status' => $publicStatuses[$statusKey],
            'note' => $notes[$statusKey],
        ];
    }

    private static function visibilityBadgeStyle(string $status): string
    {
        if ($status === 'Publicly Available') {
            return 'display:inline-block;padding:2px 8px;border-radius:999px;background:#dcfce7;color:#166534;font-weight:700;font-size:11px;';
        }

        if ($status === 'Restricted') {
            return 'display:inline-block;padding:2px 8px;border-radius:999px;background:#fef3c7;color:#92400e;font-weight:700;font-size:11px;';
        }

        return 'display:inline-block;padding:2px 8px;border-radius:999px;background:#e2e8f0;color:#334155;font-weight:700;font-size:11px;';
    }

    /**
     * @param  array<int, mixed>  $components
     * @return array<int, mixed>
     */
    private static function applyVisibilityHints(array $components, string $modelType): array
    {
        foreach ($components as $index => $component) {
            if (!$component instanceof Forms\Components\Component) {
                continue;
            }

            if (method_exists($component, 'getName') && method_exists($component, 'hint') && method_exists($component, 'hintColor')) {
                $name = (string) $component->getName();
                if ($name !== '') {
                    $visibility = static::visibilityForField($modelType, $name);
                    $shortStatus = $visibility['status'] === 'Publicly Available'
                        ? 'Public'
                        : ($visibility['status'] === 'Private' ? 'Private' : 'Restricted');

                    $component->hint($shortStatus);

                    if ($shortStatus === 'Public') {
                        $component->hintColor('success');
                    } elseif ($shortStatus === 'Restricted') {
                        $component->hintColor('warning');
                    } else {
                        $component->hintColor('secondary');
                    }
                }
            }

            if (method_exists($component, 'getChildComponents') && method_exists($component, 'schema')) {
                $children = $component->getChildComponents();
                if (!empty($children)) {
                    $component->schema(static::applyVisibilityHints($children, $modelType));
                }
            }

            $components[$index] = $component;
        }

        return $components;
    }

    private static function valueOptions(array $values): array
    {
        return collect($values)
            ->mapWithKeys(fn ($value) => [(string) $value => (string) $value])
            ->all();
    }

    private static function flattenOptionValues(array $options): array
    {
        $flattened = [];

        $walker = function (array $items) use (&$walker, &$flattened): void {
            foreach ($items as $key => $value) {
                if (is_array($value)) {
                    $walker($value);
                    continue;
                }

                if (is_numeric($key)) {
                    $flattened[(string) $value] = (string) $value;
                } else {
                    $flattened[(string) $key] = (string) $value;
                }
            }
        };

        $walker($options);

        return $flattened;
    }

    /**
     * @param  array<int, mixed>  $components
     * @return array<int, mixed>
     */
    private static function filterFieldComponentsForTable(array $components, string $table): array
    {
        $columns = static::getTableColumns($table);

        $filtered = [];
        foreach ($components as $component) {
            if (!$component instanceof Forms\Components\Component) {
                continue;
            }

            if ($component instanceof Field) {
                $name = (string) $component->getName();
                if ($name !== '' && !in_array($name, $columns, true)) {
                    continue;
                }
            }

            if (method_exists($component, 'getChildComponents') && method_exists($component, 'schema')) {
                $children = $component->getChildComponents();
                if (!empty($children)) {
                    $component->schema(static::filterFieldComponentsForTable($children, $table));
                }
            }

            $filtered[] = $component;
        }

        return $filtered;
    }

    /**
     * @return array<int, string>
     */
    private static function getTableColumns(string $table): array
    {
        if (!array_key_exists($table, static::$tableColumnsCache)) {
            static::$tableColumnsCache[$table] = Schema::hasTable($table)
                ? Schema::getColumnListing($table)
                : [];
        }

        return static::$tableColumnsCache[$table];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}'),
            'view' => Pages\ViewUser::route('/{record}/view'),
        ];
    }    
}
