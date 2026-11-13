<?php
declare(strict_types=1);

function idmatch_demo_data(): array
{
    return [
        'stats' => [
            'total_users' => 2480,
            'verified_users' => 1892,
            'active_profiles' => 1634,
            'new_this_month' => 287,
        ],
        'gender' => [
            'male' => 58,
            'female' => 40,
            'other' => 2,
        ],
        'users' => [
            ['id'=>'#10048','name'=>'Sarah Khan','email'=>'sarah@example.com','gender'=>'Female','joined'=>'Sep 12, 2025','status'=>'Active'],
            ['id'=>'#10047','name'=>'Ahmed Raza','email'=>'ahmed@example.com','gender'=>'Male','joined'=>'Sep 12, 2025','status'=>'Active'],
            ['id'=>'#10046','name'=>'Ayesha Malik','email'=>'ayesha@example.com','gender'=>'Female','joined'=>'Sep 11, 2025','status'=>'Active'],
            ['id'=>'#10045','name'=>'Bilal Ahmed','email'=>'bilal@example.com','gender'=>'Male','joined'=>'Sep 11, 2025','status'=>'Pending'],
            ['id'=>'#10044','name'=>'Fatima Noor','email'=>'fatima@example.com','gender'=>'Female','joined'=>'Sep 10, 2025','status'=>'Active'],
        ],
        'activity' => [
            ['title'=>'New user registered','meta'=>'Sarah Khan','time'=>'2 minutes ago','type'=>'user'],
            ['title'=>'Profile verified','meta'=>'Ahmed Raza','time'=>'12 minutes ago','type'=>'verify'],
            ['title'=>'Support ticket opened','meta'=>'User #10042','time'=>'25 minutes ago','type'=>'support'],
            ['title'=>'New blog post published','meta'=>'10 Dating Tips for Success','time'=>'1 hour ago','type'=>'content'],
            ['title'=>'Subscription upgraded','meta'=>'Fatima Noor - Premium','time'=>'2 hours ago','type'=>'payment'],
        ],
        'system' => [
            ['name'=>'Database','status'=>'Online'],
            ['name'=>'Payment Gateway','status'=>'Online'],
            ['name'=>'Email Service','status'=>'Online'],
            ['name'=>'File Storage','status'=>'Online'],
            ['name'=>'AI Matching Engine','status'=>'Online'],
        ],
    ];
}
