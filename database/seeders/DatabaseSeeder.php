<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user only
        User::firstOrCreate(
            ['email' => 'admin@idlmatch.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12341234'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Seed all world countries from API
        $this->command->info('Fetching countries from API...');
        
        try {
            $response = Http::get('https://countriesnow.space/api/v0.1/countries');
            
            if ($response->successful()) {
                $countries = $response->json()['data'] ?? [];
                $count = 0;
                
                foreach ($countries as $countryData) {
                    Country::firstOrCreate(['name' => $countryData['country']]);
                    $count++;
                }
                
                $this->command->info("Seeded {$count} countries.");
            } else {
                $this->command->warn('API request failed. Seeding fallback countries.');
                $this->seedFallbackCountries();
            }
        } catch (\Exception $e) {
            Log::error("Country API failed: " . $e->getMessage());
            $this->command->warn('API error. Seeding fallback countries.');
            $this->seedFallbackCountries();
        }

        // Seed demo/editorial content for user and admin panels.
        $this->call(ContentDemoSeeder::class);
    }

    private function seedFallbackCountries()
    {
        $countries = [
            'Afghanistan', 'Albania', 'Algeria', 'Argentina', 'Australia', 'Austria',
            'Bahrain', 'Bangladesh', 'Belgium', 'Brazil', 'Canada', 'China', 'Denmark',
            'Egypt', 'Finland', 'France', 'Germany', 'Greece', 'India', 'Indonesia',
            'Iran', 'Iraq', 'Ireland', 'Italy', 'Japan', 'Jordan', 'Kenya', 'Kuwait',
            'Lebanon', 'Malaysia', 'Mexico', 'Morocco', 'Netherlands', 'New Zealand',
            'Nigeria', 'Norway', 'Oman', 'Pakistan', 'Philippines', 'Poland', 'Portugal',
            'Qatar', 'Russia', 'Saudi Arabia', 'Singapore', 'South Africa', 'South Korea',
            'Spain', 'Sri Lanka', 'Sweden', 'Switzerland', 'Thailand', 'Turkey', 'UAE',
            'UK', 'USA', 'Vietnam'
        ];
        
        foreach ($countries as $country) {
            Country::firstOrCreate(['name' => $country]);
        }
        
        $this->command->info('Seeded ' . count($countries) . ' fallback countries.');
    }
}
