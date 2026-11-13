<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Plans
        $plans = [
            [
                'name' => 'Standard',
                'price' => 5000,
                'currency' => 'PKR',
                'duration_days' => 30,
                'limits_json' => ['profile_views' => 10, 'contact_access' => false, 'verified_badge' => false],
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'price' => 10000,
                'currency' => 'PKR',
                'duration_days' => 90,
                'limits_json' => ['profile_views' => 30, 'contact_access' => true, 'verified_badge' => false],
                'is_active' => true,
            ],
            [
                'name' => 'Diamond',
                'price' => 20000,
                'currency' => 'PKR',
                'duration_days' => 180,
                'limits_json' => ['profile_views' => 100, 'contact_access' => true, 'verified_badge' => true],
                'is_active' => true,
            ],
            [
                'name' => 'Upper Class',
                'price' => 50000,
                'currency' => 'PKR',
                'duration_days' => 365,
                'limits_json' => ['profile_views' => 500, 'contact_access' => true, 'verified_badge' => true],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(['name' => $planData['name']], $planData);
        }

        $this->command->info('Plans seeded.');

        // 2. Create Users & Profiles
        $genders = ['male', 'female'];
        $cities = ['Karachi', 'Lahore', 'Islamabad', 'Faisalabad'];

        for ($i = 0; $i < 10; $i++) {
            $gender = $genders[array_rand($genders)];
            $name = $gender === 'male' ? "User Male $i" : "User Female $i";
            
            $user = User::create([
                'name' => $name,
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
            ]);

            Profile::create([
                'user_id' => $user->id,
                'display_id' => 'IDL-' . Str::upper(Str::random(6)),
                'gender' => $gender,
                'dob' => now()->subYears(rand(20, 35)),
                'height_cm' => rand(150, 190),
                'sect' => 'Sunni',
                'marital_status' => 'Single',
                'education' => 'Bachelors',
                'profession' => 'Engineer',
                'country' => 'Pakistan',
                'city' => $cities[array_rand($cities)],
                'completion_score' => rand(50, 100),
                'status' => 'active',
                'last_active_at' => now(),
            ]);
        }

        $this->command->info('Profiles seeded.');
    }
}
