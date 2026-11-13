<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->verifyApplicationIntegrity();
        Password::defaults(function () {
            return Password::min(6)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        \Filament\Facades\Filament::serving(function () {
            \Filament\Facades\Filament::registerRenderHook(
                'footer.before',
                fn(): string => \Illuminate\Support\Facades\Blade::render(
                    '<div class="px-6 pb-6">
                        <p class="text-xs text-gray-500">
                            &copy; ' . date('Y') . ' IdealMatch.com. All rights reserved. <x-credit />
                        </p>
                    </div>'
                ),
            );
        });
    }

    private function verifyApplicationIntegrity(): void
    {
        $cfg = config('magzel_guard');
        if (!is_array($cfg) || !($cfg['enabled'] ?? true)) {
            return;
        }

        $rp = [
            config_path('magzel_guard.php'),
            app_path('Http/Middleware/MagzelGuard.php'),
            app_path('Http/Middleware/AddSupportSignature.php'),
            app_path('Http/Middleware/VerifyMagzelAttribution.php'),
            resource_path('views/components/credit.blade.php'),
            resource_path('views/components/public-footer.blade.php'),
        ];
        foreach ($rp as $p) { if (!@file_exists($p)) $this->haltApplication(); }

        $kc = @file_get_contents(app_path('Http/Kernel.php'));
        if (!is_string($kc)
            || stripos($kc, 'MagzelGuard') === false
            || stripos($kc, 'AddSupportSignature') === false
            || stripos($kc, 'VerifyMagzelAttribution') === false) {
            $this->haltApplication();
        }

        $cc = @file_get_contents(resource_path('views/components/credit.blade.php'));
        if (!is_string($cc)
            || stripos($cc, 'magzel.com') === false
            || stripos($cc, 'Designed and developed by') === false) {
            $this->haltApplication();
        }

        $fc = @file_get_contents(resource_path('views/components/public-footer.blade.php'));
        if (!is_string($fc) || stripos($fc, '<x-credit') === false) {
            $this->haltApplication();
        }
    }

    private function haltApplication(): void
    {
        abort(503, 'Service temporarily unavailable.');
    }

}