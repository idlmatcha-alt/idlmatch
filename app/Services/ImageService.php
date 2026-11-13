<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * High-quality profile photo optimization preset.
     * Keeps more detail for card/detail rendering on desktop and retina displays.
     */
    public function optimizeProfile(UploadedFile $file, string $directory): string
    {
        return $this->optimize($file, $directory, 2500, 100);
    }

    /**
     * Optimize an uploaded image: orientate, resize, sharpen, and convert to WebP.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $width
     * @param int $quality
     * @return string The relative path to the optimized image.
     */
    public function optimize(UploadedFile $file, string $directory, int $width = 1400, int $quality = 90): string
    {
        $filename = Str::random(40) . '.webp';
        $path = $directory . '/' . $filename;

        // Create the directory if it doesn't exist
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $image = Image::make($file)
            ->orientate()
            ->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', $quality);

        Storage::disk('public')->put($path, (string) $image);

        return $path;
    }

    /**
     * Delete an image from storage.
     *
     * @param string|null $path
     * @return void
     */
    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
