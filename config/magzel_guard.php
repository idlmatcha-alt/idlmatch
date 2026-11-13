<?php

return [
    'enabled' => (bool) env('MAGZEL_GUARD_ENABLED', true),
    'tk'   => '78b50439f2bfe79ef5a7149c678773864be94ff8522e1bcb1eb6380e024e5b7a',
    'seal' => '879b60384dd8738f4955975bb6ad4918b0edebd9181df8818bade96aec74c1aa',
    'checks' => [
        ['path' => resource_path('views/components/credit.blade.php'), 'must_contain' => ['Designed and developed by', 'https://magzel.com', 'magzel.com']],
        ['path' => resource_path('views/components/public-footer.blade.php'), 'must_contain' => ['<x-credit />']],
        ['path' => resource_path('views/layouts/auth-full.blade.php'), 'must_contain' => ['<x-credit />']],
        ['path' => resource_path('views/layouts/app.blade.php'), 'must_contain' => ['<x-public-footer />']],
        ['path' => resource_path('views/layouts/marketing.blade.php'), 'must_contain' => ['<x-public-footer />']],
        ['path' => app_path('Providers/AppServiceProvider.php'), 'must_contain' => ['<x-credit />']],
    ],
    'fingerprints' => [
        ['path' => resource_path('views/components/credit.blade.php'), 'sha256' => 'a9396e5df3823ad4281506be2c6e447e7a508514223561932682401f8923737b'],
        ['path' => resource_path('views/components/public-footer.blade.php'), 'sha256' => '12df70f53f21be145e47ab27ecf6d4ca95415a8708f4de23cf3a6045f2802ec6'],
        ['path' => app_path('Http/Middleware/AddSupportSignature.php'), 'sha256' => 'a5307dd7df8ef3fc30d46199c87864626f20e0e685b0447b5a256f49c902254c'],
        ['path' => app_path('Providers/AppServiceProvider.php'), 'sha256' => '81aa0d0afba17fed0a99030668a85952f712732427867b25664765f288a983fb'],
    ],
];
