<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * The blog images ship with the app in storage/app/public/blog, but on the
     * live database the image columns were empty, so every article fell back to
     * the grey "Blog Article" placeholder. Point the known posts at their image.
     *
     * Only empty values are touched, so images chosen later in the admin panel
     * are never overwritten.
     *
     * @var array<string, string>
     */
    private array $images = [
        'Questions to Ask Before Family Involvement' => 'blog/blog-01-family-involvement.jpg',
        'How to Set Realistic Partner Expectations' => 'blog/blog-02-partner-expectations.jpg',
        'Key Compatibility Areas Before Nikah' => 'blog/blog-03-compatibility-before-nikah.jpg',
        'Preparing for the First Family Meeting' => 'blog/blog-04-first-family-meeting.jpg',
        'Dealing with Rejections in a Healthy Way' => 'blog/blog-05-dealing-with-rejections.jpg',
        'Balancing Career Goals and Marriage Planning' => 'blog/blog-06-career-and-marriage.jpg',
        'Practical Red Flags to Watch in Online Matchmaking' => 'blog/blog-07-online-red-flags.jpg',
        'Creating a Matrimonial Profile That Gets Better Responses' => 'blog/blog-08-profile-that-gets-responses.jpg',
        'How to Identify Serious Matrimonial Intentions' => 'blog/blog-09-serious-intentions.jpg',
        'How to Build Trust During Matchmaking Conversations' => 'blog/blog-10-build-trust.jpg',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('blog_posts') || ! Schema::hasColumn('blog_posts', 'featured_image')) {
            return;
        }

        foreach ($this->images as $title => $image) {
            $matchingPosts = function ($query) use ($title) {
                $query->whereRaw('LOWER(TRIM(title)) = ?', [Str::lower(trim($title))]);
            };

            DB::table('blog_posts')
                ->where($matchingPosts)
                ->where(function ($query) {
                    $query->whereNull('featured_image')->orWhere('featured_image', '');
                })
                ->update(['featured_image' => $image]);

            DB::table('blog_posts')
                ->where($matchingPosts)
                ->where(function ($query) {
                    $query->whereNull('cover_image')->orWhere('cover_image', '');
                })
                ->update(['cover_image' => $image]);
        }
    }

    public function down(): void
    {
        // The backfill is content repair; removing it would bring the broken
        // placeholders back, so nothing is reverted here.
    }
};
