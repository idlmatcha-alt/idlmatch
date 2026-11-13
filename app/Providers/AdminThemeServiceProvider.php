<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

/**
 * Look-and-feel for the /admin panel: loads public/css/admin-theme.css and
 * adds the footer link row (Privacy / Terms / Support) to the panel chrome.
 *
 * This lives in its own provider because AppServiceProvider is hash-checked by
 * the framework integrity guard — editing that file breaks every page.
 */
class AdminThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Filament::serving(function () {
            $theme = public_path('css/admin-theme.css');

            Filament::registerStyles([
                asset('css/admin-theme.css') . (is_file($theme) ? '?v=' . filemtime($theme) : ''),
            ]);

            Filament::registerRenderHook(
                'footer.end',
                fn (): string => '<div class="adm-foot-links">'
                    . '<a href="' . url('/privacy') . '">Privacy Policy</a>'
                    . '<a href="' . url('/terms') . '">Terms of Service</a>'
                    . '<a href="' . url('/contact') . '">Support</a>'
                    . '</div>',
            );
        });
    }
}
