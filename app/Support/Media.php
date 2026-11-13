<?php

namespace App\Support;

class Media
{
    /**
     * URL of a resized copy of a stored image, generated once and cached on disk.
     * Keeps list/avatar views from downloading full-size member uploads.
     */
    public static function thumb(?string $path, int $width = 400): string
    {
        return self::resolve($path, $width);
    }

    /**
     * URL of the complete, original stored image (no resizing).
     */
    public static function full(?string $path): string
    {
        return self::resolve($path, PHP_INT_MAX);
    }

    private static function resolve(?string $path, int $width): string
    {
        $path = ltrim((string) $path, '/');

        if ($path === '' || preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $target = 'thumbs/' . $width . '/' . preg_replace('~\.[a-z0-9]+$~i', '.webp', $path);
        $public = storage_path('app/public/' . $target);

        // A cached thumbnail is enough on its own: the original may have been
        // removed on the server while the generated copy is still being served.
        if (is_file($public)) {
            return asset('storage/' . $target);
        }

        $source = storage_path('app/public/' . $path);

        if (!is_file($source)) {
            return asset('storage/' . $path);
        }

        if ($width === PHP_INT_MAX) {
            return asset('storage/' . $path);
        }

        self::render($source, $public, $width);

        return is_file($public) ? asset('storage/' . $target) : asset('storage/' . $path);
    }

    protected static function render(string $source, string $target, int $width): void
    {
        $im = @imagecreatefromstring((string) file_get_contents($source));

        if (!$im) {
            return;
        }

        $w = imagesx($im);
        $h = imagesy($im);

        if ($w <= $width) {
            return;
        }

        $dst = imagecreatetruecolor($width, (int) round($h * ($width / $w)));
        imagealphablending($dst, true);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $im, 0, 0, 0, 0, imagesx($dst), imagesy($dst), $w, $h);

        if (!is_dir($dir = dirname($target))) {
            mkdir($dir, 0775, true);
        }

        imagewebp($dst, $target, 78);
    }
}
