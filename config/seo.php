<?php

/*
|--------------------------------------------------------------------------
| SEO registry for IDEAL MARRIAGE CENTER (idlmatch.com)
|--------------------------------------------------------------------------
| One source of truth for titles, descriptions, keywords, robots rules,
| Open Graph data and sitemap weights, keyed by Laravel route name.
| Titles are <= 60 chars, descriptions <= 160 chars.
*/

return [
    'site' => [
        'name' => 'Ideal Marriage Center',
        'short_name' => 'IDL Match',
        'domain' => 'idlmatch.com',
        'tagline' => 'Verified rishta, matchmaking and marriage services',
        'description' => 'Ideal Marriage Center (IDL Match) is a matrimonial service for Muslims seeking verified '
            . 'brides, grooms and rishtas in Pakistan, India and overseas. Build a biodata, match profiles '
            . 'and get family-level matchmaking support.',
        'keywords' => [
            'marriage', 'rishta', 'match', 'groom', 'bride', 'dating website', 'matching website',
            'matrimonial website', 'matchmaking', 'muslim matrimonial', 'pakistani rishta',
            'biodata format', 'nikah', 'arranged marriage', 'overseas rishta',
        ],
        'language' => 'en',
        'locale' => 'en_PK',
        'theme_color' => '#9d174d',
        'og_image' => 'images/og-ideal-marriage-center.jpg',
        'og_image_width' => 1200,
        'og_image_height' => 630,
        'twitter' => '@idlmatch',
        'facebook' => 'https://www.facebook.com/idlmatch',
        'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
        'bing_site_verification' => env('BING_SITE_VERIFICATION'),
    ],

    'org' => [
        'type' => 'Organization',
        'legal_name' => 'Ideal Marriage Center',
        'email' => 'info@idlmatch.com',
        'phones' => ['+92 300 2294445', '+92 345 2450024'],
        'founding_year' => 2024,
        'area_served' => ['PK', 'IN', 'GB', 'US', 'CA', 'SA', 'AE', 'DE', 'FR', 'IT', 'RU', 'TH', 'MY', 'AU'],
        'logo' => 'images/logo-240.png',
    ],

    'defaults' => [
        'robots' => 'index,follow',
        'type' => 'website',
        'changefreq' => 'monthly',
        'priority' => '0.5',
    ],

    'pages' => [
        'home' => [
            'title' => 'Ideal Marriage Center | Rishta, Marriage & Matchmaking',
            'desc' => 'Find a life partner with verified rishta matchmaking. Browse brides and grooms, write a '
                . 'marriage biodata and get family-guided support.',
            'keys' => ['rishta', 'marriage', 'match', 'matchmaking', 'bride', 'groom', 'matrimonial website',
                'matching website', 'pakistani brides', 'muslim matrimonial', 'dating website'],
            'freq' => 'weekly',
            'pri' => '1.0',
            'jsonld' => 'webpage',
        ],
        'about' => [
            'title' => 'About Us | Ideal Marriage Center Matrimonial Service',
            'desc' => 'Meet Ideal Marriage Center, a verification-first matrimonial service. See how every rishta, '
                . 'biodata and match is checked before an introduction.',
            'keys' => ['about ideal marriage center', 'matrimonial service', 'matchmaking team', 'rishta service'],
            'freq' => 'yearly',
            'pri' => '0.6',
            'jsonld' => 'about',
        ],
        'about.our-services' => [
            'title' => 'Marriage & Matchmaking Services | IDL Match',
            'desc' => 'Rishta search, profile verification, biodata writing, family consultation, intro support and '
                . 'marriage counselling — the full marriage service, step by step.',
            'keys' => ['marriage services', 'rishta service', 'matchmaking', 'biodata making',
                'nikah consultation', 'matrimony packages'],
            'freq' => 'monthly',
            'pri' => '0.9',
            'jsonld' => 'services',
        ],
        'about.hire-consultant' => [
            'title' => 'Hire a Matchmaker or Marriage Consultant | IDL Match',
            'desc' => 'Work with an assigned rishta consultant who shortlists matches, checks families and guides you '
                . 'from first proposal to nikah. Book a consultation today.',
            'keys' => ['hire matchmaker', 'marriage consultant', 'rishta consultant', 'wedding planner'],
            'freq' => 'monthly',
            'pri' => '0.8',
        ],
        'about.grooming-consultant' => [
            'title' => 'Grooming & Presentation Consultant for Brides | IDL Match',
            'desc' => 'Image, styling and confidence coaching for brides and grooms before meetings and photos, so '
                . 'your profile and first meeting make the right impression.',
            'keys' => ['grooming consultant', 'bridal grooming', 'bride presentation', 'groom styling'],
            'freq' => 'monthly',
            'pri' => '0.6',
        ],
        'about.marriage-counseling' => [
            'title' => 'Marriage Counselling & Premarital Guidance | IDL Match',
            'desc' => 'Faith-aware premarital and post-marriage counselling for couples and families — communication, '
                . 'expectations, in-laws and conflict resolution.',
            'keys' => ['marriage counselling', 'premarital counselling', 'islamic marriage advice',
                'couples counselling'],
            'freq' => 'monthly',
            'pri' => '0.7',
        ],
        'about.legal-advisor' => [
            'title' => 'Legal Advice for Nikah & Marriage Documents | IDL Match',
            'desc' => 'Nikah registration, mahr terms, custody, visa and spousal-document guidance from a qualified '
                . 'family-law advisor for local and overseas marriages.',
            'keys' => ['nikah legal advice', 'marriage law pakistan', 'mahr agreement', 'spousal visa advice'],
            'freq' => 'monthly',
            'pri' => '0.6',
        ],
        'about.leadership' => [
            'title' => 'Leadership Team | Ideal Marriage Center',
            'desc' => 'The founders, senior matchmakers and advisors behind Ideal Marriage Center and our '
                . 'verification-first approach to rishta matchmaking.',
            'keys' => ['leadership', 'founder idl match', 'senior matchmakers'],
            'freq' => 'yearly',
            'pri' => '0.4',
        ],
        'about.affiliate' => [
            'title' => 'Affiliate & Referral Program | Earn with IDL Match',
            'desc' => 'Refer families to Ideal Marriage Center and earn commission on every paid rishta package. Free '
                . 'to join, tracked links, monthly payouts.',
            'keys' => ['affiliate program', 'matrimony referral', 'earn online pakistan', 'rishta agent'],
            'freq' => 'monthly',
            'pri' => '0.5',
        ],
        'profiles.index' => [
            'title' => 'Search Brides & Grooms | Verified Rishta Profiles',
            'desc' => 'Browse verified matrimonial biodatas and filter matches by age, city, sect, profession and '
                . 'marital status — rishta profiles from Pakistan and overseas.',
            'keys' => ['bride profiles', 'groom profiles', 'rishta search', 'matrimonial biodata',
                'matching website', 'pakistani brides', 'overseas pakistani rishta'],
            'freq' => 'daily',
            'pri' => '0.9',
            'jsonld' => 'search',
        ],
        'abroad.proposals' => [
            'title' => 'Overseas & Foreign Rishta Proposals | IDL Match',
            'desc' => 'Serious marriage proposals from Pakistanis settled abroad — UK, USA, Canada, Gulf, Europe, '
                . 'Russia and Thailand — matched with verified local profiles.',
            'keys' => ['overseas rishta', 'foreign groom', 'pga marriage', 'uk pakistani bride',
                'canada rishta', 'gulf matrimonial'],
            'freq' => 'daily',
            'pri' => '0.8',
        ],
        'elite.matrimonial' => [
            'title' => 'Elite Matrimonial Profiles | IDL Match Premium',
            'desc' => 'Hand-picked elite biodatas from educated, well-established families seeking a compatible match — '
                . 'priority introductions and dedicated consultant support.',
            'keys' => ['elite matrimonial', 'premium rishta', 'rich families match', 'doctor engineer biodata'],
            'freq' => 'daily',
            'pri' => '0.7',
        ],
        'packages.index' => [
            'title' => 'Rishta Packages & Prices in PKR | IDL Match',
            'desc' => 'Compare Basic, Silver, Gold and Foreign matchmaking packages with clear PKR prices. Pay by bank '
                . 'transfer, JazzCash or Easypaisa and start getting matches.',
            'keys' => ['matrimony package price', 'rishta package cost', 'gold plan',
                'foreign proposal package', 'jazzcash matrimonial'],
            'freq' => 'weekly',
            'pri' => '0.8',
        ],
        'success-stories.index' => [
            'title' => 'Marriage Success Stories | Real IDL Match Couples',
            'desc' => 'True stories from couples who found each other through Ideal Marriage Center — how the rishta '
                . 'started, the families met and why the match worked.',
            'keys' => ['marriage success stories', 'rishta success', 'matched couples', 'idl match stories'],
            'freq' => 'monthly',
            'pri' => '0.7',
        ],
        'blog.index' => [
            'title' => 'Marriage & Rishta Advice Blog | Ideal Marriage Center',
            'desc' => 'Practical guides on choosing a match, compatibility before nikah, family meetings, profile '
                . 'red flags and building a matrimonial biodata that gets replies.',
            'keys' => ['marriage advice', 'rishta tips', 'nikah compatibility', 'matrimonial profile tips',
                'dating website advice'],
            'freq' => 'weekly',
            'pri' => '0.7',
        ],
        'faqs' => [
            'title' => 'Rishta & Marriage FAQs | How IDL Match Works',
            'desc' => 'How registration, profile verification, sending interest, contact details, privacy settings and '
                . 'paid rishta packages work at Ideal Marriage Center.',
            'keys' => ['rishta faq', 'matrimony help', 'how to send interest', 'matchmaking questions'],
            'freq' => 'yearly',
            'pri' => '0.5',
            'jsonld' => 'faq',
        ],
        'safety' => [
            'title' => 'Matrimonial Safety & Scam Prevention | IDL Match',
            'desc' => 'Stay safe while searching for rishtas online: verify identities, never send money, report fake '
                . 'profiles and follow these rules before any family meeting.',
            'keys' => ['matrimonial safety', 'rishta scam', 'fake profile', 'online matchmaking danger'],
            'freq' => 'yearly',
            'pri' => '0.6',
        ],
        'career' => [
            'title' => 'Careers & Matchmaker Jobs | Ideal Marriage Center',
            'desc' => 'Join the Ideal Marriage Center team — open roles for matchmakers, profile verifiers, counsellors '
                . 'and support staff in Lahore and remote.',
            'keys' => ['matchmaker jobs', 'marriage consultant career', 'idl match jobs'],
            'freq' => 'monthly',
            'pri' => '0.4',
        ],
        'contact' => [
            'title' => 'Contact IDL Match | Rishta Help & Support',
            'desc' => 'Talk to the Ideal Marriage Center team about a rishta, verification, packages or a complaint. '
                . 'Call, WhatsApp or message us, or subscribe for tips.',
            'keys' => ['contact idl match', 'rishta help', 'matrimony support number', 'marriage office'],
            'freq' => 'yearly',
            'pri' => '0.6',
            'jsonld' => 'contact',
        ],
        'idltech' => [
            'title' => 'IDLTech | Matchmaking Technology by IDL Match',
            'desc' => 'The compatibility scoring, verification pipeline and privacy controls behind IDL Match — how our '
                . 'matching website ranks proposals and protects member data.',
            'keys' => ['matching website technology', 'compatibility algorithm', 'matrimony software', 'idltech'],
            'freq' => 'monthly',
            'pri' => '0.5',
        ],
        'register' => [
            'title' => 'Create a Free Matrimonial Profile | IDL Match',
            'desc' => 'Sign up free, add your biodata, upload photos and start receiving verified rishta proposals for '
                . 'marriage in Pakistan or abroad.',
            'keys' => ['free matrimonial registration', 'bride groom registration', 'rishta account',
                'dating website signup'],
            'freq' => 'yearly',
            'pri' => '0.8',
        ],
        'coming-soon' => [
            'title' => 'IDL Match Mobile App — Coming Soon',
            'desc' => 'Our Android and iOS matchmaking app is in development. Use the fully responsive website in the '
                . 'meantime and subscribe for the launch announcement.',
            'keys' => ['matrimony app', 'rishta app coming soon'],
            'robots' => 'noindex,follow',
            'freq' => 'yearly',
            'pri' => '0.2',
        ],
        'privacy' => [
            'title' => 'Privacy Policy | Ideal Marriage Center',
            'desc' => 'How Ideal Marriage Center collects, stores and protects your matrimonial data, photos, contact '
                . 'details and privacy choices.',
            'keys' => ['matrimonial privacy policy'],
            'robots' => 'index,nofollow',
            'freq' => 'yearly',
            'pri' => '0.2',
        ],
        'terms' => [
            'title' => 'Terms & Conditions | Ideal Marriage Center',
            'desc' => 'Membership rules, package terms, refund policy, conduct and content rules for using Ideal '
                . 'Marriage Center matchmaking services.',
            'keys' => ['matrimony terms'],
            'robots' => 'index,nofollow',
            'freq' => 'yearly',
            'pri' => '0.2',
        ],
        'login' => ['title' => 'Member Login | Ideal Marriage Center',
            'desc' => 'Sign in to your Ideal Marriage Center account to check rishta proposals, interests and messages.',
            'keys' => ['matrimony login'], 'robots' => 'noindex,follow', 'sitemap' => false],
        'password.request' => ['title' => 'Reset Your Password | IDL Match',
            'desc' => 'Reset the password for your Ideal Marriage Center account.',
            'keys' => [], 'robots' => 'noindex,follow', 'sitemap' => false],
        'password.confirm' => ['title' => 'Confirm Password | IDL Match',
            'desc' => 'Confirm your password to continue.', 'keys' => [],
            'robots' => 'noindex,nofollow', 'sitemap' => false],
        'verification.notice' => ['title' => 'Verify Your Email | IDL Match',
            'desc' => 'Confirm your email address to activate your matrimonial profile.',
            'keys' => [], 'robots' => 'noindex,follow', 'sitemap' => false],
        'dashboard' => ['title' => 'Member Dashboard | IDL Match',
            'desc' => 'Your matches, interests and subscription at a glance.', 'keys' => [],
            'robots' => 'noindex,nofollow', 'sitemap' => false],
        'favorites.index' => ['title' => 'Shortlisted Profiles | IDL Match',
            'desc' => 'Profiles you have saved for later.', 'keys' => [],
            'robots' => 'noindex,nofollow', 'sitemap' => false],
        'threads.index' => ['title' => 'Community Threads | IDL Match',
            'desc' => 'Member discussions on marriage, rishta and family topics.', 'keys' => [],
            'robots' => 'noindex,follow', 'sitemap' => false],
        'success-stories.create' => ['title' => 'Share Your Success Story | IDL Match',
            'desc' => 'Submit your marriage success story for review.', 'keys' => [],
            'robots' => 'noindex,follow', 'sitemap' => false],
        'install' => ['title' => 'Installer | IDL Match', 'desc' => 'Application installer.',
            'keys' => [], 'robots' => 'noindex,nofollow', 'sitemap' => false],
    ],

    /* Profile biodata pages: member privacy wins over long-tail indexing. */
    'profile_page' => [
        'robots' => 'noindex,follow',
        'sitemap' => false,
    ],
];
