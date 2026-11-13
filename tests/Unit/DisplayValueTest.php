<?php

namespace Tests\Unit;

use App\Support\DisplayValue;
use PHPUnit\Framework\TestCase;

class DisplayValueTest extends TestCase
{
    public function test_it_formats_scalar_and_empty_values(): void
    {
        $this->assertSame('Lahore', DisplayValue::text('Lahore', '-'));
        $this->assertSame('-', DisplayValue::text('', '-'));
        $this->assertSame('-', DisplayValue::text(null, '-'));
    }

    public function test_it_formats_list_values_without_throwing(): void
    {
        $value = ['Pakistan', '', null, ['Lahore', 'Karachi']];

        $this->assertSame('Pakistan, Lahore, Karachi', DisplayValue::text($value, 'Any'));
    }

    public function test_it_formats_boolean_values(): void
    {
        $this->assertSame('Yes', DisplayValue::text(true, '-'));
        $this->assertSame('No', DisplayValue::text(false, '-'));
    }
}
