<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_gallery_add_button_and_mobile_upload_link(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'display_id' => 'AB1234',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('profiles.show', $profile->display_id));

        $response->assertOk();
        $response->assertSee('Photo Gallery');
        $response->assertSee('Upload Here');
        $response->assertSee('Upload via QR Code');
        $response->assertSee('No photos yet. Add photos from your phone to build your gallery.');
        $response->assertSee('/profile/mobile-upload/' . $user->id . '/', false);
        $response->assertSee('type=gallery', false);
    }

    public function test_non_owner_only_sees_approved_gallery_photos(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $owner->id,
            'display_id' => 'CD5678',
            'status' => 'active',
        ]);

        Photo::create([
            'profile_id' => $profile->id,
            'path' => 'profiles/' . $profile->id . '/approved.webp',
            'status' => 'approved',
            'is_primary' => false,
        ]);

        Photo::create([
            'profile_id' => $profile->id,
            'path' => 'profiles/' . $profile->id . '/pending.webp',
            'status' => 'pending',
            'is_primary' => false,
        ]);

        $response = $this->get(route('profiles.show', $profile->display_id));

        $response->assertOk();
        $response->assertSee('profiles/' . $profile->id . '/approved.webp', false);
        $response->assertDontSee('profiles/' . $profile->id . '/pending.webp', false);
        $response->assertDontSee('Add Photos');
    }

    public function test_owner_can_delete_gallery_photo(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'display_id' => 'EF9012',
            'status' => 'active',
        ]);

        $photo = Photo::create([
            'profile_id' => $profile->id,
            'path' => 'profiles/' . $profile->id . '/gallery-delete.webp',
            'status' => 'approved',
            'is_primary' => false,
        ]);

        $response = $this->actingAs($user)->delete(route('photos.destroy', $photo));

        $response->assertRedirect();
        $this->assertSoftDeleted('photos', ['id' => $photo->id]);
    }

    public function test_deleting_primary_photo_promotes_a_remaining_photo(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'display_id' => 'GH3456',
            'status' => 'active',
        ]);

        $primary = Photo::create([
            'profile_id' => $profile->id,
            'path' => 'profiles/' . $profile->id . '/primary.webp',
            'status' => 'approved',
            'is_primary' => true,
        ]);

        $gallery = Photo::create([
            'profile_id' => $profile->id,
            'path' => 'profiles/' . $profile->id . '/gallery.webp',
            'status' => 'approved',
            'is_primary' => false,
        ]);

        $response = $this->actingAs($user)->delete(route('photos.destroy', $primary));

        $response->assertRedirect();
        $this->assertSoftDeleted('photos', ['id' => $primary->id]);
        $this->assertDatabaseHas('photos', [
            'id' => $gallery->id,
            'is_primary' => true,
        ]);
    }

    public function test_gallery_upload_is_limited_to_four_photos(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'display_id' => 'IJ7890',
            'status' => 'active',
        ]);

        foreach (range(1, 4) as $index) {
            Photo::create([
                'profile_id' => $profile->id,
                'path' => 'profiles/' . $profile->id . '/gallery-' . $index . '.webp',
                'status' => 'approved',
                'is_primary' => false,
            ]);
        }

        $response = $this->actingAs($user)->post(route('profile.update-section', 'gallery'), [
            'photo' => UploadedFile::fake()->image('new-gallery.jpg', 800, 800),
        ]);

        $response->assertRedirect(route('profiles.show', $profile->display_id));
        $response->assertSessionHas('error');
        $this->assertSame(4, Photo::where('profile_id', $profile->id)->where('is_primary', false)->count());
    }
}
