<?php

namespace Tests\Unit;

use App\Models\Profile;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ProfileLastSeenTextTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_formats_today_last_seen_text(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-20 18:00:00'));

        $profile = new Profile([
            'last_active_at' => Carbon::now()->subHours(6),
        ]);

        $this->assertSame('Last seen today, 6 hours ago', $profile->getLastSeenText());
    }

    public function test_it_formats_yesterday_last_seen_text(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-20 18:00:00'));

        $profile = new Profile([
            'last_active_at' => Carbon::now()->subDay(),
        ]);

        $this->assertSame('Last seen yesterday', $profile->getLastSeenText());
    }

    public function test_it_formats_days_ago_last_seen_text(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-20 18:00:00'));

        $profile = new Profile([
            'last_active_at' => Carbon::now()->subDays(3),
        ]);

        $this->assertSame('Last seen 3 days ago', $profile->getLastSeenText());
    }

    public function test_it_formats_tomorrow_last_seen_text_for_future_timestamp(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-20 18:00:00'));

        $profile = new Profile([
            'last_active_at' => Carbon::now()->addDay(),
        ]);

        $this->assertSame('Last seen tomorrow', $profile->getLastSeenText());
    }
}
