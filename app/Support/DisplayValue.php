<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Collection;
use Stringable;
use Traversable;

class DisplayValue
{
    public static function text($value, string $fallback = '-'): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return self::fromList($value, $fallback);
        }

        if ($value instanceof Collection) {
            return self::fromList($value->all(), $fallback);
        }

        if ($value instanceof Traversable) {
            return self::fromList(iterator_to_array($value), $fallback);
        }

        if ($value instanceof Stringable) {
            $string = trim((string) $value);
            return $string !== '' ? $string : $fallback;
        }

        if (is_object($value)) {
            return $fallback;
        }

        if ($value === null) {
            return $fallback;
        }

        $string = trim((string) $value);
        return $string !== '' ? $string : $fallback;
    }

    private static function fromList(array $value, string $fallback): string
    {
        $items = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $nested = self::fromList($item, '');
                if ($nested !== '') {
                    $items[] = $nested;
                }
                continue;
            }

            if ($item instanceof DateTimeInterface) {
                $items[] = $item->format('Y-m-d H:i:s');
                continue;
            }

            if (is_bool($item)) {
                $items[] = $item ? 'Yes' : 'No';
                continue;
            }

            if ($item instanceof Stringable || is_scalar($item)) {
                $string = trim((string) $item);
                if ($string !== '') {
                    $items[] = $string;
                }
            }
        }

        if ($items === []) {
            return $fallback;
        }

        return implode(', ', $items);
    }
}
