<?php

namespace App\Filament\Pages\Auth;

use Filament\Http\Livewire\Auth\Login as BaseLogin;
use Illuminate\Contracts\View\View;

class Login extends BaseLogin
{
    public function render(): View
    {
        return view('filament.auth.login')
            ->layout('filament::components.layouts.card', [
                'title' => __('filament::login.title'),
            ]);
    }
}
