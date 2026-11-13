<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Services\ImageService;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function setPrimary(Photo $photo)
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        if ((int) $photo->profile_id !== (int) $profile->id) {
            abort(403);
        }

        Photo::where('profile_id', $profile->id)->update(['is_primary' => false]);
        $photo->is_primary = true;
        $photo->save();

        return redirect()->back()->with('status', 'Primary photo updated.');
    }

    public function destroy(Photo $photo, ImageService $imageService)
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }

        if ((int) $photo->profile_id !== (int) $profile->id) {
            abort(403);
        }

        $wasPrimary = (bool) $photo->is_primary;
        $imageService->delete($photo->path);
        $photo->delete();

        if ($wasPrimary) {
            $fallback = Photo::where('profile_id', $profile->id)->latest('id')->first();
            if ($fallback) {
                Photo::where('profile_id', $profile->id)->update(['is_primary' => false]);
                $fallback->update(['is_primary' => true]);
            }
        }

        return redirect()->back()->with('status', $wasPrimary ? 'Main photo deleted.' : 'Gallery photo deleted.');
    }
}
