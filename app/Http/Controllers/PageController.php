<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Profile;
use App\Models\Subscriber;
use App\Models\SuccessStory;
use App\Http\Requests\ContactRequest;
use App\Support\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function home()
    {
        if (auth()->check()) {
            return redirect()->route('profiles.index');
        }

        $storiesCacheKey = 'home_stories_v3_' . now()->format('YmdH');
        $stories = \Illuminate\Support\Facades\Cache::remember($storiesCacheKey, 600, function () {
            // Always show the 3 featured stories with photos first
            $featured = SuccessStory::select(['id', 'couple_names', 'story', 'cover_image', 'status', 'created_at'])
                ->where('status', 'approved')
                ->whereNotNull('cover_image')
                ->where('cover_image', '!=', '')
                ->inRandomOrder()
                ->take(3)
                ->get();
            if ($featured->count() >= 3) {
                return $featured;
            }
            return SuccessStory::select(['id', 'couple_names', 'story', 'cover_image', 'status', 'created_at'])
                ->where('status', 'approved')
                ->inRandomOrder()
                ->take(3)
                ->get();
        });

        $profiles = \Illuminate\Support\Facades\Cache::remember('home_profiles_v3', 600, function () {
            return Profile::select([
                'id',
                'user_id',
                'display_id',
                'gender',
                'dob',
                'height_cm',
                'profession',
                'city',
                'country',
                'religion',
                'caste',
                'status',
                'created_at',
            ])
                ->visibleToUsers()
                ->whereHas('user.activeSubscription')
                ->with(['primaryPhoto', 'user.activeSubscription.plan'])
                ->latest()
                ->take(6)
                ->get();
        });

        $postsCacheKey = 'home_posts_v3_' . now()->format('YmdH');
        $posts = \Illuminate\Support\Facades\Cache::remember($postsCacheKey, 600, function () {
            return BlogPost::select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'cover_image', 'status', 'published_at', 'created_at'])
                ->where('status', 'published')
                ->where(function ($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->inRandomOrder()
                ->take(18)
                ->get()
                ->unique(fn (BlogPost $post) => Str::lower(trim((string) $post->title)))
                ->take(3)
                ->values();
        });

        return view('pages.home', compact('stories', 'profiles', 'posts'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(ContactRequest $request)
    {
        Mail::raw($request->input('message'), function ($message) use ($request) {
            $message->to(config('mail.from.address'))
                ->replyTo($request->input('email'))
                ->subject('Website enquiry from ' . $request->input('name'));
        });

        Tracking::meta('Lead', ['content_name' => 'contact_form']);

        return redirect()->route('contact')->with('status', 'Message sent.');
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'subscriber_email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'max:0'],
        ], [
            'subscriber_email.required' => 'Please enter your email address.',
            'subscriber_email.email' => 'Please enter a valid email address.',
        ]);

        $subscriber = Subscriber::firstOrCreate(
            ['email' => Str::lower(trim($data['subscriber_email']))],
            ['source' => 'contact-page']
        );

        return redirect()->route('contact')->with(
            'subscribe_status',
            $subscriber->wasRecentlyCreated
                ? 'Thanks for subscribing! Watch your inbox for matchmaking tips and offers.'
                : 'You are already subscribed with this email address.'
        );
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function faqs()
    {
        return view('pages.faqs', ['faqs' => config('faqs.items', [])]);
    }

    public function safety()
    {
        return view('pages.safety');
    }

    public function career()
    {
        return view('pages.career');
    }

    public function idltech()
    {
        return view('pages.idltech');
    }

    public function service(string $page)
    {
        $pages = $this->aboutPages();

        abort_unless(isset($pages[$page]), 404);

        return view('pages.service', ['page' => $pages[$page]]);
    }

    private function aboutPages(): array
    {
        return [
            'hire-consultant' => [
                'eyebrow' => 'Hire Consultant',
                'title' => 'Your Personal Matchmaking Consultant',
                'lead' => 'Work one-to-one with a senior consultant who understands your family\'s values, screens every introduction and manages the process from the first call to the final decision.',
                'image' => 'images/about/hire-consultant.jpg',
                'highlights' => [
                    ['title' => 'One dedicated consultant', 'text' => 'A named consultant stays with your case from the first call to the final meeting — no call centres, no starting over.'],
                    ['title' => 'Hand-screened introductions', 'text' => 'Every proposal is checked for identity, family background and genuine intent before it reaches you.'],
                    ['title' => 'Family coordination', 'text' => 'We speak with both families, align expectations and arrange meetings respectfully and on time.'],
                    ['title' => 'Complete discretion', 'text' => 'Photos and contact details stay private until you approve an introduction.'],
                ],
                'steps' => ['Free consultation call', 'Profile & preference session', 'Curated introductions', 'Meeting & decision support'],
                'cta_title' => 'Ready to work with a consultant?',
                'cta_text' => 'Tell us about yourself and your family, and we will match you with the right consultant within one working day.',
                'cta_label' => 'Book a consultation',
                'cta_url' => null,
                'cta_route' => 'contact',
            ],
            'our-services' => [
                'eyebrow' => 'Our Services',
                'title' => 'Matchmaking, Done the Family Way',
                'lead' => 'Everything you need to find a life partner — verified profiles, human matchmaking, safe introductions and support all the way to the wedding.',
                'image' => 'images/about/our-services.jpg',
                'highlights' => [
                    ['title' => 'Verified profiles', 'text' => 'CNIC and photo verification, manual moderation and activity checks keep the community genuine.'],
                    ['title' => 'Human matchmaking', 'text' => 'Our team suggests compatible rishtas based on values, family background and life goals — not just filters.'],
                    ['title' => 'Safe introductions', 'text' => 'Contact details are revealed only with both sides\' consent, and meetings can be arranged by our staff.'],
                    ['title' => 'Post-rishta support', 'text' => 'Guidance for engagement, nikah paperwork and the wedding steps that follow.'],
                ],
                'steps' => ['Create your profile', 'Complete verification', 'Receive curated matches', 'Meet with confidence'],
                'cta_title' => 'Start your search today',
                'cta_text' => 'Create a free profile and let our matchmakers do the searching with you.',
                'cta_label' => 'Create free profile',
                'cta_url' => null,
                'cta_route' => 'register',
            ],
            'legal-advisor' => [
                'eyebrow' => 'Legal Advisor',
                'title' => 'Legal Guidance for Your Marriage',
                'lead' => 'From nikah registration to immigration paperwork, our empanelled legal advisors help your family complete every step correctly and on record.',
                'image' => 'images/about/legal-advisor.jpg',
                'highlights' => [
                    ['title' => 'Nikah & registration', 'text' => 'Guidance on nikah-nama requirements, registration with the union council and correct documentation.'],
                    ['title' => 'Marriage contract review', 'text' => 'Clear explanation of contract terms and conditions before anything is signed.'],
                    ['title' => 'Immigration & visas', 'text' => 'Support with spouse visas, affidavits and embassy-required marriage documents.'],
                    ['title' => 'Confidential consultations', 'text' => 'Private sessions by phone, video or in person with qualified legal professionals.'],
                ],
                'steps' => ['Share your situation', 'Document check', 'Advisor consultation', 'Step-by-step filing'],
                'cta_title' => 'Speak to a legal advisor',
                'cta_text' => 'Legal advice is provided by independent, qualified advisors. Request a confidential consultation today.',
                'cta_label' => 'Request consultation',
                'cta_url' => null,
                'cta_route' => 'contact',
            ],
            'leadership' => [
                'eyebrow' => 'Leadership',
                'title' => 'The People Behind Ideal Marriage Center',
                'lead' => 'A team of matchmakers, counsellors, safety specialists and technologists united by one goal: helping families find the right partner with dignity.',
                'image' => 'images/about/leadership.jpg',
                'highlights_eyebrow' => 'Structure',
                'highlights_title' => 'How the centre is organised',
                'highlights' => [
                    ['title' => 'Office of the Founder', 'text' => 'Sets the family-first direction of the centre and reviews every service we launch.'],
                    ['title' => 'Matchmaking Directorate', 'text' => 'Senior matchmakers and consultants who lead rishta searches across Pakistan and abroad.'],
                    ['title' => 'Client Care', 'text' => 'A dedicated team handling member questions, family coordination and after-care.'],
                    ['title' => 'Trust & Safety', 'text' => 'Verification analysts and moderators who review profiles, reports and privacy requests.'],
                ],
                'steps' => ['Family first', 'Privacy always', 'Accountability', 'Respect for all'],
                'steps_title' => 'What we hold ourselves to',
                'cta_title' => 'Want to join our team?',
                'cta_text' => 'We are always looking for thoughtful people who care about families and matchmaking done properly.',
                'cta_label' => 'View careers',
                'cta_url' => null,
                'cta_route' => 'career',
            ],
            'grooming-consultant' => [
                'eyebrow' => 'Grooming Consultant',
                'title' => 'Look Your Best for the Big Decision',
                'lead' => 'First impressions matter in rishta meetings. Our grooming consultants help you present yourself with confidence — from wardrobe and photos to conversation.',
                'image' => 'images/about/grooming-consultant.jpg',
                'highlights' => [
                    ['title' => 'Wardrobe & styling', 'text' => 'Advice on outfits, colours and grooming suited to your family\'s culture and the occasion.'],
                    ['title' => 'Profile photography', 'text' => 'Guidance on taking natural, respectable photos that represent you honestly.'],
                    ['title' => 'Conversation coaching', 'text' => 'Practice the questions and answers that come up in first family meetings.'],
                    ['title' => 'Pre-meeting preparation', 'text' => 'A short session before the meeting so you arrive calm, prepared and yourself.'],
                ],
                'steps' => ['Book a session', 'Personal assessment', 'Coaching & practice', 'Meeting-ready'],
                'cta_title' => 'Book a grooming session',
                'cta_text' => 'Sessions are available in person and by video, for both men and women.',
                'cta_label' => 'Book a session',
                'cta_url' => null,
                'cta_route' => 'contact',
            ],
            'marriage-counseling' => [
                'eyebrow' => 'Marriage Counseling',
                'title' => 'Counseling Before and After the Nikah',
                'lead' => 'Confidential sessions with qualified counsellors to discuss expectations, families and communication — so the marriage starts on solid ground.',
                'image' => 'images/about/marriage-counseling.jpg',
                'highlights' => [
                    ['title' => 'Pre-marital counseling', 'text' => 'Honest conversations about money, family roles, faith and future plans before commitment.'],
                    ['title' => 'Expectation mapping', 'text' => 'Helping both families align on traditions, living arrangements and timelines.'],
                    ['title' => 'Couple communication', 'text' => 'Practical tools for handling disagreement with respect from the very beginning.'],
                    ['title' => 'Post-marriage support', 'text' => 'Follow-up sessions for the first year, because support should not end at the wedding.'],
                ],
                'steps' => ['Private intake', 'Counsellor matching', 'Sessions at your pace', 'Ongoing support'],
                'cta_title' => 'Talk to a counsellor',
                'cta_text' => 'All sessions are completely confidential. Speak with us to find the right counsellor for you.',
                'cta_label' => 'Request a session',
                'cta_url' => null,
                'cta_route' => 'contact',
            ],
            'affiliate' => [
                'eyebrow' => 'Affiliate Program',
                'title' => 'Refer Families. Earn Rewards.',
                'lead' => 'If you run a community page, marriage bureau or simply know families looking for the right match, partner with us and earn for every successful referral.',
                'image' => 'images/about/affiliate.jpg',
                'highlights_eyebrow' => 'Partner benefits',
                'highlights_title' => 'Why partner with us',
                'highlights' => [
                    ['title' => 'Generous commission', 'text' => 'Earn a competitive commission on every subscription that comes from your referrals.'],
                    ['title' => 'Simple tracking', 'text' => 'A personal referral link and a clear record of every click and signup.'],
                    ['title' => 'Marketing support', 'text' => 'Ready-made banners, captions and guidance for sharing responsibly.'],
                    ['title' => 'Prompt payouts', 'text' => 'Monthly payouts by bank transfer once your referrals are confirmed.'],
                ],
                'steps' => ['Apply to join', 'Get your link', 'Share with families', 'Earn on signups'],
                'cta_title' => 'Join the affiliate program',
                'cta_text' => 'Tell us a little about your audience and we will send you your referral link and welcome pack.',
                'cta_label' => 'Become an affiliate',
                'cta_url' => 'mailto:affiliate@idlmatch.com',
                'cta_route' => null,
            ],
        ];
    }
}
