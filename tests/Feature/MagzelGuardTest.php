<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MagzelGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_magzel_guard_allows_request_when_integrity_checks_pass(): void
    {
        config()->set('magzel_guard.enabled', true);

        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_magzel_guard_allows_admin_login_when_integrity_checks_pass(): void
    {
        config()->set('magzel_guard.enabled', true);

        $adminPath = '/' . trim((string) config('filament.path', 'admin'), '/');
        $response = $this->get($adminPath . '/login');

        $response->assertOk();
    }

    public function test_magzel_guard_blocks_request_when_required_signature_is_missing(): void
    {
        config()->set('magzel_guard.enabled', true);
        config()->set('magzel_guard.checks', [
            [
                'path' => base_path('composer.json'),
                'must_contain' => ['this_signature_does_not_exist_5700'],
            ],
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(503);
        $response->assertSee('Access token issue 5700');
        $response->assertSee('if you are visitor come back later');
        $response->assertSee('if you are owner contact hosting or dev company magzel');
    }

    public function test_magzel_guard_blocks_admin_login_when_required_signature_is_missing(): void
    {
        config()->set('magzel_guard.enabled', true);
        config()->set('magzel_guard.checks', [
            [
                'path' => base_path('composer.json'),
                'must_contain' => ['this_signature_does_not_exist_5700'],
            ],
        ]);

        $adminPath = '/' . trim((string) config('filament.path', 'admin'), '/');
        $response = $this->get($adminPath . '/login');

        $response->assertStatus(503);
        $response->assertSee('Access token issue 5700');
    }

    public function test_magzel_guard_blocks_request_when_fingerprint_check_fails(): void
    {
        config()->set('magzel_guard.enabled', true);
        config()->set('magzel_guard.fingerprints', [
            [
                'path' => base_path('composer.json'),
                'sha256' => str_repeat('0', 64),
            ],
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(503);
        $response->assertSee('Access token issue 5700');
    }
}
