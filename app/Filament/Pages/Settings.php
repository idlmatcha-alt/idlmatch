<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title = 'Account Settings';
    protected static ?int $navigationSort = 100;
    protected static string $view = 'filament.pages.settings';

    public $email;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount(): void
    {
        $this->email = Auth::user()->email;
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Update Email')
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->label('Email Address'),
                ])
                ->columns(1),

            Forms\Components\Section::make('Change Password')
                ->schema([
                    Forms\Components\TextInput::make('current_password')
                        ->password()
                        ->label('Current Password')
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('new_password')
                        ->password()
                        ->label('New Password')
                        ->minLength(8)
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('new_password_confirmation')
                        ->password()
                        ->label('Confirm New Password')
                        ->same('new_password')
                        ->dehydrated(false),
                ])
                ->columns(1),
        ];
    }

    public function updateEmail(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'unique:users,email,' . Auth::id()],
        ]);

        Auth::user()->update([
            'email' => $this->email,
        ]);

        Notification::make()
            ->title('Email updated successfully!')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($this->current_password, Auth::user()->password)) {
            Notification::make()
                ->title('Current password is incorrect!')
                ->danger()
                ->send();
            return;
        }

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        Notification::make()
            ->title('Password updated successfully!')
            ->success()
            ->send();
    }
}
