<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\SuccessStory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ContentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();
        $adminId = User::query()
            ->whereRaw("LOWER(TRIM(COALESCE(role, ''))) = 'admin'")
            ->value('id');

        $authorId = $adminId ?: ($userIds[0] ?? null);
        $storyImagePool = $this->collectImagePool(['success-stories', 'profiles']);
        $blogImagePool = $this->collectImagePool(['blog', 'success-stories']);

        $this->seedSuccessStories($userIds, $storyImagePool);
        $this->seedBlogPosts($authorId, $blogImagePool);
    }

    /**
     * @return array<int, string>
     */
    private function collectImagePool(array $directories): array
    {
        $root = storage_path('app/public');
        $paths = [];

        foreach ($directories as $directory) {
            $fullPath = $root . DIRECTORY_SEPARATOR . $directory;
            if (!is_dir($fullPath)) {
                continue;
            }

            foreach (File::allFiles($fullPath) as $file) {
                $extension = strtolower((string) $file->getExtension());
                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    continue;
                }

                $paths[] = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @param  array<int, int>  $userIds
     * @param  array<int, string>  $imagePool
     */
    private function seedSuccessStories(array $userIds, array $imagePool): void
    {
        $maleNames = ['Ahmed', 'Bilal', 'Usman', 'Hamza', 'Saad', 'Talha', 'Farhan', 'Imran', 'Zain', 'Hassan'];
        $femaleNames = ['Ayesha', 'Fatima', 'Maryam', 'Hira', 'Sana', 'Amna', 'Zoya', 'Nimra', 'Maham', 'Rabia'];
        $cities = ['Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Multan', 'Faisalabad', 'Peshawar', 'Sialkot'];

        $approvedTarget = 8;
        $pendingTarget = 2;

        $approvedCount = SuccessStory::query()->where('status', 'approved')->count();
        $pendingCount = SuccessStory::query()->where('status', 'pending')->count();

        $usedCovers = SuccessStory::query()
            ->whereNotNull('cover_image')
            ->where('cover_image', '!=', '')
            ->pluck('cover_image')
            ->map(fn ($path) => (string) $path)
            ->unique()
            ->values()
            ->all();

        $approvedOpenings = [
            'We found each other here after both our families had given up on arranged introductions.',
            'A mutual interest in the same city and the same values is what made us stop scrolling.',
            'We matched on a Friday and spoke every evening after that.',
            'Our profiles were detailed enough that the first conversation felt like an introduction, not an interview.',
            'We asked the hard questions early — family, work, where we would live — and the answers lined up.',
            'Respect on both sides, and families that actually listened, is what made this work.',
            'We took the verification badges seriously; that trust is why we are here today.',
            'Distance was the only doubt, and it dissolved after the first call.',
        ];

        for ($i = $approvedCount; $i < $approvedTarget; $i++) {
            $groom = $maleNames[array_rand($maleNames)];
            $bride = $femaleNames[array_rand($femaleNames)];
            $city = $cities[array_rand($cities)];
            $opening = $approvedOpenings[$i % count($approvedOpenings)];

            SuccessStory::create([
                'submitted_by' => $userIds !== [] ? $userIds[array_rand($userIds)] : null,
                'groom_name' => $groom,
                'bride_name' => $bride,
                'couple_names' => "{$groom} & {$bride}",
                'story' => "{$opening} We met through Ideal Marriage Center in {$city}, and after "
                    . "thoughtful conversations and family meetings we decided to get married. "
                    . 'We are thankful for a secure and serious platform that helped us start this journey together.',
                'cover_image' => $this->uniqueStoryImage($imagePool, $usedCovers),
                'status' => 'approved',
            ]);
        }

        for ($i = $pendingCount; $i < $pendingTarget; $i++) {
            $groom = $maleNames[array_rand($maleNames)];
            $bride = $femaleNames[array_rand($femaleNames)];
            $city = $cities[array_rand($cities)];

            SuccessStory::create([
                'submitted_by' => $userIds !== [] ? $userIds[array_rand($userIds)] : null,
                'groom_name' => $groom,
                'bride_name' => $bride,
                'couple_names' => "{$groom} & {$bride}",
                'story' => "Our families connected through Ideal Marriage Center in {$city}. "
                    . "The process was respectful and transparent, and we are sharing our journey with gratitude.",
                'cover_image' => $this->uniqueStoryImage($imagePool, $usedCovers),
                'status' => 'pending',
            ]);
        }
    }

    /**
     * @param  array<int, string>  $imagePool
     */
    private function seedBlogPosts(?int $authorId, array $imagePool): void
    {
        $topics = [
            'How to Build Trust During Matchmaking Conversations',
            'Questions to Ask Before Family Involvement',
            'How to Identify Serious Matrimonial Intentions',
            'Balancing Career Goals and Marriage Planning',
            'Smart Ways to Verify Profile Authenticity',
            'Preparing for the First Family Meeting',
            'Dealing with Rejections in a Healthy Way',
            'Creating a Matrimonial Profile That Gets Better Responses',
            'Key Compatibility Areas Before Nikah',
            'Practical Red Flags to Watch in Online Matchmaking',
            'Communication Habits for Long-Term Marriage Success',
            'How to Set Realistic Partner Expectations',
        ];

        $publishedTarget = 10;
        $draftTarget = 2;
        $publishedCount = BlogPost::query()->where('status', 'published')->count();
        $draftCount = BlogPost::query()->where('status', 'draft')->count();
        $existingTitles = BlogPost::query()
            ->pluck('title')
            ->map(fn ($title) => Str::lower(trim((string) $title)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $availableTopics = collect($topics)
            ->shuffle()
            ->filter(fn (string $title) => !in_array(Str::lower(trim($title)), $existingTitles, true))
            ->values()
            ->all();

        $publishedNeeded = max(0, $publishedTarget - $publishedCount);
        $publishedTopics = array_slice($availableTopics, 0, $publishedNeeded);

        foreach ($publishedTopics as $title) {
            $slug = $this->uniqueBlogSlug($title);
            $content = $this->blogBody($title);

            BlogPost::create([
                'author_id' => $authorId,
                'title' => $title,
                'slug' => $slug,
                'excerpt' => Str::limit(strip_tags($content), 180),
                'content' => $content,
                'cover_image' => $this->randomImage($imagePool),
                'featured_image' => $this->randomImage($imagePool),
                'meta_title' => Str::limit($title . ' | Ideal Marriage Center', 78, ''),
                'meta_description' => Str::limit(strip_tags($content), 190),
                'published_at' => now()->subDays(rand(1, 160)),
                'status' => 'published',
            ]);
        }

        $draftNeeded = max(0, $draftTarget - $draftCount);
        $draftSourceTopics = array_slice($availableTopics, count($publishedTopics), $draftNeeded);

        foreach ($draftSourceTopics as $baseTitle) {
            $title = $baseTitle . ' (Draft)';
            $slug = $this->uniqueBlogSlug($title);
            $content = $this->blogBody($title);

            BlogPost::create([
                'author_id' => $authorId,
                'title' => $title,
                'slug' => $slug,
                'excerpt' => Str::limit(strip_tags($content), 180),
                'content' => $content,
                'cover_image' => $this->randomImage($imagePool),
                'featured_image' => null,
                'meta_title' => Str::limit($title . ' | Ideal Marriage Center', 78, ''),
                'meta_description' => Str::limit(strip_tags($content), 190),
                'published_at' => null,
                'status' => 'draft',
            ]);
        }
    }

    private function uniqueBlogSlug(string $title): string
    {
        do {
            $slug = Str::slug($title) . '-' . Str::lower(Str::random(5));
        } while (BlogPost::query()->where('slug', $slug)->exists());

        return $slug;
    }

    private function randomImage(array $imagePool): ?string
    {
        if ($imagePool === []) {
            return null;
        }

        return $imagePool[array_rand($imagePool)];
    }

    /**
     * Pick a cover image that has not been used by an existing success story yet,
     * so newly seeded stories never duplicate each other's photos.
     *
     * @param  array<int, string>  $imagePool
     * @param  array<int, string>  $usedCovers
     */
    private function uniqueStoryImage(array $imagePool, array &$usedCovers): ?string
    {
        $available = array_values(array_diff($imagePool, $usedCovers));

        if ($available === []) {
            return $this->randomImage($imagePool);
        }

        $image = $available[array_rand($available)];
        $usedCovers[] = $image;

        return $image;
    }

    private function blogBody(string $title): string
    {
        $intro = "Many people focus only on biodata, but strong marriage decisions require clarity, communication, and values alignment.";
        $middle = "On Ideal Marriage Center, users get better outcomes when they ask practical questions, involve families at the right time, and verify intentions early.";
        $closing = "This guide explains simple actions you can take to avoid confusion and move towards a respectful, long-term match.";

        return '<h2>' . e($title) . '</h2>'
            . '<p>' . e($intro) . '</p>'
            . '<p>' . e($middle) . '</p>'
            . '<p>' . e($closing) . '</p>';
    }
}
