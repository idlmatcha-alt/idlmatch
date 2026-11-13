<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Filament\Resources\ProfileResource\RelationManagers;
use App\Models\Profile;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'All Profiles';
    protected static ?string $navigationGroup = 'Profiles';
    protected static ?int $navigationSort = 4;

    /** "All Profiles" is one of the reference sidebar items. */
    protected static bool $shouldRegisterNavigation = true;

    /** Makes profiles findable from the search box at the top of the panel. */
    protected static ?string $recordTitleAttribute = 'display_id';

    public static function getGloballySearchableAttributes(): array
    {
        return ['display_id', 'user.name', 'user.email'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return ['Member' => optional($record->user)->name];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user.activeSubscription.plan']);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ========== ACCOUNT INFO ==========
                Forms\Components\Section::make('Account Information')
                    ->schema([
                        Forms\Components\TextInput::make('display_id')->label('Profile ID')->disabled(),
                        Forms\Components\TextInput::make('user.email')->label('Email')->disabled(),
                        Forms\Components\TextInput::make('user.name')->label('Name')->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'banned' => 'Banned',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('cnic_verified')
                            ->label('CNIC Verified')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('verification_status', 'verified');
                                }
                            }),
                        Forms\Components\TextInput::make('completion_score')->numeric()->disabled(),
                    ])->columns(3),

                // ========== PERSONAL DETAILS ==========
                Forms\Components\Section::make('Personal Details')
                    ->schema([
                        Forms\Components\Select::make('gender')
                            ->options(['male' => 'Male', 'female' => 'Female'])
                            ->required(),
                        Forms\Components\DatePicker::make('dob')->label('Date of Birth')->required(),
                        Forms\Components\Select::make('marital_status')
                            ->options([
                                'Single' => 'Single',
                                'Divorced' => 'Divorced',
                                'Widowed' => 'Widowed',
                                'Awaiting Divorce' => 'Awaiting Divorce',
                            ])->required(),
                        Forms\Components\TextInput::make('height_cm')->label('Height (cm)')->numeric(),
                        Forms\Components\TextInput::make('disability'),
                    ])->columns(3),

                // ========== RELIGION & IDENTITY ==========
                Forms\Components\Section::make('Religion & Identity')
                    ->schema([
                        Forms\Components\TextInput::make('religion'),
                        Forms\Components\TextInput::make('sect'),
                        Forms\Components\TextInput::make('caste'),
                    ])->columns(3),

                // ========== EDUCATION & CAREER ==========
                Forms\Components\Section::make('Education & Career')
                    ->schema([
                        Forms\Components\TextInput::make('education')->label('Qualification'),
                        Forms\Components\TextInput::make('major_course')->label('Major'),
                        Forms\Components\TextInput::make('college'),
                        Forms\Components\TextInput::make('university'),
                        Forms\Components\TextInput::make('profession'),
                        Forms\Components\TextInput::make('job_type')->label('Job Type'),
                        Forms\Components\TextInput::make('nature_of_job')->label('Nature of Job'),
                        Forms\Components\TextInput::make('income'),
                    ])->columns(4),

                // ========== FAMILY DETAILS ==========
                Forms\Components\Section::make('Family Details')
                    ->schema([
                        Forms\Components\TextInput::make('father_name'),
                        Forms\Components\TextInput::make('father_occupation'),
                        Forms\Components\TextInput::make('mother_occupation'),
                        Forms\Components\TextInput::make('brothers_count')->numeric(),
                        Forms\Components\TextInput::make('married_brothers_count')->label('Married Brothers')->numeric(),
                        Forms\Components\TextInput::make('sisters_count')->numeric(),
                        Forms\Components\TextInput::make('married_sisters_count')->label('Married Sisters')->numeric(),
                        Forms\Components\TextInput::make('married_siblings_count')->label('Married Siblings')->numeric(),
                        Forms\Components\Textarea::make('family_details')->columnSpanFull(),
                    ])->columns(3),

                // ========== ADDRESS & PROPERTY ==========
                Forms\Components\Section::make('Address & Property')
                    ->schema([
                        Forms\Components\TextInput::make('country'),
                        Forms\Components\TextInput::make('state'),
                        Forms\Components\TextInput::make('city'),
                        Forms\Components\TextInput::make('home_town'),
                        Forms\Components\TextInput::make('home_location')->label('Area/Locality'),
                        Forms\Components\Select::make('home_status')
                            ->options(['Own' => 'Own', 'Rent' => 'Rent']),
                        Forms\Components\TextInput::make('home_size'),
                        Forms\Components\Textarea::make('other_properties')->columnSpanFull(),
                    ])->columns(4),

                // ========== CONTACT ==========
                Forms\Components\Section::make('Contact')
                    ->schema([
                        Forms\Components\TextInput::make('mobile_no'),
                        Forms\Components\TextInput::make('phone'),
                    ])->columns(2),

                // ========== BIO ==========
                Forms\Components\Section::make('About Me')
                    ->schema([
                        Forms\Components\Textarea::make('bio')->rows(4)->columnSpanFull(),
                    ]),

                // ========== PARTNER PREFERENCES ==========
                Forms\Components\Section::make('Partner Preferences')
                    ->relationship('preferences')
                    ->schema([
                        Forms\Components\TextInput::make('desired_age_min')->label('Min Age')->numeric(),
                        Forms\Components\TextInput::make('desired_age_max')->label('Max Age')->numeric(),
                        Forms\Components\TextInput::make('desired_height_min')->label('Min Height (cm)')->numeric(),
                        Forms\Components\TextInput::make('desired_height_max')->label('Max Height (cm)')->numeric(),
                        Forms\Components\TextInput::make('desired_marital_status')->label('Marital Status'),
                        Forms\Components\TextInput::make('desired_religion')->label('Religion'),
                        Forms\Components\TextInput::make('desired_sect')->label('Sect'),
                        Forms\Components\TextInput::make('desired_caste')->label('Caste'),
                        Forms\Components\TextInput::make('desired_education')->label('Education'),
                        Forms\Components\TextInput::make('desired_city')->label('City'),
                        Forms\Components\TextInput::make('desired_country')->label('Country'),
                        Forms\Components\TextInput::make('desired_profession')->label('Profession'),
                        Forms\Components\Textarea::make('additional_preferences')->label('Other Requirements')->columnSpanFull(),
                    ])->columns(4),

                // ========== VERIFICATION IMAGES ==========
                Forms\Components\Section::make('Verification Documents')
                    ->schema([
                        Forms\Components\FileUpload::make('selfie')
                            ->disk('public')
                            ->image()
                            ->directory('selfies')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('cnic_front')
                            ->disk('public')
                            ->image()
                            ->directory('cnic')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('cnic_back')
                            ->disk('public')
                            ->image()
                            ->directory('cnic')
                            ->visibility('public'),
                        Forms\Components\Select::make('verification_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'verified') {
                                    $set('cnic_verified', true);
                                } elseif ($state === 'rejected' || $state === 'pending') {
                                    $set('cnic_verified', false);
                                }
                            })
                            ->required(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->visible(fn ($get) => $get('verification_status') === 'rejected')
                            ->required(fn ($get) => $get('verification_status') === 'rejected')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryPhoto.path')
                    ->label('Photo')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('display_id')->label('ID')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('gender')->sortable(),
                Tables\Columns\TextColumn::make('city')->sortable(),
                Tables\Columns\BadgeColumn::make('user.activeSubscription.plan.name')
                    ->label('Package')
                    ->colors([
                        'success' => fn($state) => $state && $state !== 'Free',
                        'secondary' => fn($state) => !$state || $state === 'Free',
                    ])
                    ->default('Free'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'secondary' => 'inactive',
                        'danger' => 'banned',
                    ]),
                Tables\Columns\BadgeColumn::make('verification_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\IconColumn::make('cnic_verified')->boolean()->label('Verified'),
                Tables\Columns\TextColumn::make('completion_score')->label('Score')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'banned' => 'Banned',
                    ]),
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Verification')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female']),
                Tables\Filters\TernaryFilter::make('cnic_verified')->label('CNIC Verified'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'view' => Pages\ViewProfile::route('/{record}'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}
