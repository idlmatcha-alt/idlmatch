<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicBlogVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_blog_pages_show_only_published_and_not_future_posts(): void
    {
        Cache::flush();

        $publishedPost = BlogPost::create([
            'title' => 'Public Published Post',
            'slug' => 'public-published-post',
            'excerpt' => 'Visible blog excerpt',
            'content' => '<p>Visible blog content</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $draftPost = BlogPost::create([
            'title' => 'Hidden Draft Post',
            'slug' => 'hidden-draft-post',
            'excerpt' => 'Draft excerpt',
            'content' => '<p>Draft content</p>',
            'status' => 'draft',
        ]);

        $scheduledPost = BlogPost::create([
            'title' => 'Future Scheduled Post',
            'slug' => 'future-scheduled-post',
            'excerpt' => 'Scheduled excerpt',
            'content' => '<p>Scheduled content</p>',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Public Published Post')
            ->assertDontSee('Hidden Draft Post')
            ->assertDontSee('Future Scheduled Post');

        $this->get(route('blog.show', $publishedPost))
            ->assertOk()
            ->assertSee('Public Published Post');

        $this->get(route('blog.show', $draftPost))->assertNotFound();
        $this->get(route('blog.show', $scheduledPost))->assertNotFound();
    }

    public function test_home_header_and_sections_expose_blog_for_public_users(): void
    {
        Cache::flush();

        BlogPost::create([
            'title' => 'Homepage Blog Post',
            'slug' => 'homepage-blog-post',
            'excerpt' => 'Homepage excerpt',
            'content' => '<p>Homepage blog content</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertSee('Latest Blog Articles')
            ->assertSee('Homepage Blog Post')
            ->assertSee('Blog Resources')
            ->assertSee(route('blog.show', 'homepage-blog-post'), false);
    }

    public function test_blog_show_sidebar_prefers_profiles_from_article_country_context(): void
    {
        Cache::flush();

        $pkUser = User::factory()->create(['name' => 'Sana Pakistan']);
        $pkProfile = Profile::factory()->create([
            'user_id' => $pkUser->id,
            'display_id' => 'PK1001',
            'country' => 'Pakistan',
            'status' => 'active',
        ]);

        $trUser = User::factory()->create(['name' => 'Aylin Turkiye']);
        $trProfile = Profile::factory()->create([
            'user_id' => $trUser->id,
            'display_id' => 'TR1001',
            'country' => 'Turkey',
            'status' => 'active',
        ]);

        $blogPost = BlogPost::create([
            'title' => 'Best Marriage Trends in Pakistan for 2026',
            'slug' => 'pakistan-marriage-trends-2026',
            'excerpt' => 'Country-focused article',
            'content' => '<p>This article discusses families and proposals in Pakistan.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $this->get(route('blog.show', $blogPost))
            ->assertOk()
            ->assertSee('Profiles from Pakistan')
            ->assertSee(route('profiles.show', $pkProfile->display_id), false)
            ->assertSee($pkProfile->display_id)
            ->assertSee($trProfile->display_id);
    }
}
