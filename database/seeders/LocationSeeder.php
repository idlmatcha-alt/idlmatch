<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LocationSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks for trancate
        Schema::disableForeignKeyConstraints();
        City::truncate();
        State::truncate();
        Country::truncate();
        Schema::enableForeignKeyConstraints();

        $this->command->info('Seeding 250+ countries...');

        // Use a static list of all countries to ensure it always works without API
        $countries = [
            ['name' => 'Afghanistan', 'code' => 'AF'], ['name' => 'Albania', 'code' => 'AL'], ['name' => 'Algeria', 'code' => 'DZ'],
            ['name' => 'Andorra', 'code' => 'AD'], ['name' => 'Angola', 'code' => 'AO'], ['name' => 'Antigua and Barbuda', 'code' => 'AG'],
            ['name' => 'Argentina', 'code' => 'AR'], ['name' => 'Armenia', 'code' => 'AM'], ['name' => 'Australia', 'code' => 'AU'],
            ['name' => 'Austria', 'code' => 'AT'], ['name' => 'Azerbaijan', 'code' => 'AZ'], ['name' => 'Bahamas', 'code' => 'BS'],
            ['name' => 'Bahrain', 'code' => 'BH'], ['name' => 'Bangladesh', 'code' => 'BD'], ['name' => 'Barbados', 'code' => 'BB'],
            ['name' => 'Belarus', 'code' => 'BY'], ['name' => 'Belgium', 'code' => 'BE'], ['name' => 'Belize', 'code' => 'BZ'],
            ['name' => 'Benin', 'code' => 'BJ'], ['name' => 'Bhutan', 'code' => 'BT'], ['name' => 'Bolivia', 'code' => 'BO'],
            ['name' => 'Bosnia and Herzegovina', 'code' => 'BA'], ['name' => 'Botswana', 'code' => 'BW'], ['name' => 'Brazil', 'code' => 'BR'],
            ['name' => 'Brunei', 'code' => 'BN'], ['name' => 'Bulgaria', 'code' => 'BG'], ['name' => 'Burkina Faso', 'code' => 'BF'],
            ['name' => 'Burundi', 'code' => 'BI'], ['name' => 'Cabo Verde', 'code' => 'CV'], ['name' => 'Cambodia', 'code' => 'KH'],
            ['name' => 'Cameroon', 'code' => 'CM'], ['name' => 'Canada', 'code' => 'CA'], ['name' => 'Central African Republic', 'code' => 'CF'],
            ['name' => 'Chad', 'code' => 'TD'], ['name' => 'Chile', 'code' => 'CL'], ['name' => 'China', 'code' => 'CN'],
            ['name' => 'Colombia', 'code' => 'CO'], ['name' => 'Comoros', 'code' => 'KM'], ['name' => 'Congo', 'code' => 'CG'],
            ['name' => 'Costa Rica', 'code' => 'CR'], ['name' => 'Croatia', 'code' => 'HR'], ['name' => 'Cuba', 'code' => 'CU'],
            ['name' => 'Cyprus', 'code' => 'CY'], ['name' => 'Czech Republic', 'code' => 'CZ'], ['name' => 'Denmark', 'code' => 'DK'],
            ['name' => 'Djibouti', 'code' => 'DJ'], ['name' => 'Dominica', 'code' => 'DM'], ['name' => 'Dominican Republic', 'code' => 'DO'],
            ['name' => 'Ecuador', 'code' => 'EC'], ['name' => 'Egypt', 'code' => 'EG'], ['name' => 'El Salvador', 'code' => 'SV'],
            ['name' => 'Equatorial Guinea', 'code' => 'GQ'], ['name' => 'Eritrea', 'code' => 'ER'], ['name' => 'Estonia', 'code' => 'EE'],
            ['name' => 'Eswatini', 'code' => 'SZ'], ['name' => 'Ethiopia', 'code' => 'ET'], ['name' => 'Fiji', 'code' => 'FJ'],
            ['name' => 'Finland', 'code' => 'FI'], ['name' => 'France', 'code' => 'FR'], ['name' => 'Gabon', 'code' => 'GA'],
            ['name' => 'Gambia', 'code' => 'GM'], ['name' => 'Georgia', 'code' => 'GE'], ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'Ghana', 'code' => 'GH'], ['name' => 'Greece', 'code' => 'GR'], ['name' => 'Grenada', 'code' => 'GD'],
            ['name' => 'Guatemala', 'code' => 'GT'], ['name' => 'Guinea', 'code' => 'GN'], ['name' => 'Guyana', 'code' => 'GY'],
            ['name' => 'Haiti', 'code' => 'HT'], ['name' => 'Honduras', 'code' => 'HN'], ['name' => 'Hungary', 'code' => 'HU'],
            ['name' => 'Iceland', 'code' => 'IS'], ['name' => 'India', 'code' => 'IN'], ['name' => 'Indonesia', 'code' => 'ID'],
            ['name' => 'Iran', 'code' => 'IR'], ['name' => 'Iraq', 'code' => 'IQ'], ['name' => 'Ireland', 'code' => 'IE'],
            ['name' => 'Israel', 'code' => 'IL'], ['name' => 'Italy', 'code' => 'IT'], ['name' => 'Jamaica', 'code' => 'JM'],
            ['name' => 'Japan', 'code' => 'JP'], ['name' => 'Jordan', 'code' => 'JO'], ['name' => 'Kazakhstan', 'code' => 'KZ'],
            ['name' => 'Kenya', 'code' => 'KE'], ['name' => 'Kiribati', 'code' => 'KI'], ['name' => 'Kuwait', 'code' => 'KW'],
            ['name' => 'Kyrgyzstan', 'code' => 'KG'], ['name' => 'Laos', 'code' => 'LA'], ['name' => 'Latvia', 'code' => 'LV'],
            ['name' => 'Lebanon', 'code' => 'LB'], ['name' => 'Lesotho', 'code' => 'LS'], ['name' => 'Liberia', 'code' => 'LR'],
            ['name' => 'Libya', 'code' => 'LY'], ['name' => 'Liechtenstein', 'code' => 'LI'], ['name' => 'Lithuania', 'code' => 'LT'],
            ['name' => 'Luxembourg', 'code' => 'LU'], ['name' => 'Madagascar', 'code' => 'MG'], ['name' => 'Malawi', 'code' => 'MW'],
            ['name' => 'Malaysia', 'code' => 'MY'], ['name' => 'Maldives', 'code' => 'MV'], ['name' => 'Mali', 'code' => 'ML'],
            ['name' => 'Malta', 'code' => 'MT'], ['name' => 'Mexico', 'code' => 'MX'], ['name' => 'Moldova', 'code' => 'MD'],
            ['name' => 'Monaco', 'code' => 'MC'], ['name' => 'Mongolia', 'code' => 'MN'], ['name' => 'Montenegro', 'code' => 'ME'],
            ['name' => 'Morocco', 'code' => 'MA'], ['name' => 'Mozambique', 'code' => 'MZ'], ['name' => 'Myanmar', 'code' => 'MM'],
            ['name' => 'Namibia', 'code' => 'NA'], ['name' => 'Nauru', 'code' => 'NR'], ['name' => 'Nepal', 'code' => 'NP'],
            ['name' => 'Netherlands', 'code' => 'NL'], ['name' => 'New Zealand', 'code' => 'NZ'], ['name' => 'Nicaragua', 'code' => 'NI'],
            ['name' => 'Niger', 'code' => 'NE'], ['name' => 'Nigeria', 'code' => 'NG'], ['name' => 'North Korea', 'code' => 'KP'],
            ['name' => 'Norway', 'code' => 'NO'], ['name' => 'Oman', 'code' => 'OM'], ['name' => 'Pakistan', 'code' => 'PK'],
            ['name' => 'Palau', 'code' => 'PW'], ['name' => 'Palestine', 'code' => 'PS'], ['name' => 'Panama', 'code' => 'PA'],
            ['name' => 'Papua New Guinea', 'code' => 'PG'], ['name' => 'Paraguay', 'code' => 'PY'], ['name' => 'Peru', 'code' => 'PE'],
            ['name' => 'Philippines', 'code' => 'PH'], ['name' => 'Poland', 'code' => 'PL'], ['name' => 'Portugal', 'code' => 'PT'],
            ['name' => 'Qatar', 'code' => 'QA'], ['name' => 'Romania', 'code' => 'RO'], ['name' => 'Russia', 'code' => 'RU'],
            ['name' => 'Rwanda', 'code' => 'RW'], ['name' => 'Saint Kitts and Nevis', 'code' => 'KN'], ['name' => 'Saint Lucia', 'code' => 'LC'],
            ['name' => 'Samoa', 'code' => 'WS'], ['name' => 'San Marino', 'code' => 'SM'], ['name' => 'Saudi Arabia', 'code' => 'SA'],
            ['name' => 'Senegal', 'code' => 'SN'], ['name' => 'Serbia', 'code' => 'RS'], ['name' => 'Seychelles', 'code' => 'SC'],
            ['name' => 'Sierra Leone', 'code' => 'SL'], ['name' => 'Singapore', 'code' => 'SG'], ['name' => 'Slovakia', 'code' => 'SK'],
            ['name' => 'Slovenia', 'code' => 'SI'], ['name' => 'Solomon Islands', 'code' => 'SB'], ['name' => 'Somalia', 'code' => 'SO'],
            ['name' => 'South Africa', 'code' => 'ZA'], ['name' => 'South Korea', 'code' => 'KR'], ['name' => 'South Sudan', 'code' => 'SS'],
            ['name' => 'Spain', 'code' => 'ES'], ['name' => 'Sri Lanka', 'code' => 'LK'], ['name' => 'Sudan', 'code' => 'SD'],
            ['name' => 'Suriname', 'code' => 'SR'], ['name' => 'Sweden', 'code' => 'SE'], ['name' => 'Switzerland', 'code' => 'CH'],
            ['name' => 'Syria', 'code' => 'SY'], ['name' => 'Taiwan', 'code' => 'TW'], ['name' => 'Tajikistan', 'code' => 'TJ'],
            ['name' => 'Tanzania', 'code' => 'TZ'], ['name' => 'Thailand', 'code' => 'TH'], ['name' => 'Timor-Leste', 'code' => 'TL'],
            ['name' => 'Togo', 'code' => 'TG'], ['name' => 'Tonga', 'code' => 'TO'], ['name' => 'Trinidad and Tobago', 'code' => 'TT'],
            ['name' => 'Tunisia', 'code' => 'TN'], ['name' => 'Turkey', 'code' => 'TR'], ['name' => 'Turkmenistan', 'code' => 'TM'],
            ['name' => 'Tuvalu', 'code' => 'TV'], ['name' => 'Uganda', 'code' => 'UG'], ['name' => 'Ukraine', 'code' => 'UA'],
            ['name' => 'UAE', 'code' => 'AE'], ['name' => 'United Kingdom', 'code' => 'GB'], ['name' => 'USA', 'code' => 'US'],
            ['name' => 'Uruguay', 'code' => 'UY'], ['name' => 'Uzbekistan', 'code' => 'UZ'], ['name' => 'Vanuatu', 'code' => 'VU'],
            ['name' => 'Venezuela', 'code' => 'VE'], ['name' => 'Vietnam', 'code' => 'VN'], ['name' => 'Yemen', 'code' => 'YE'],
            ['name' => 'Zambia', 'code' => 'ZM'], ['name' => 'Zimbabwe', 'code' => 'ZW']
        ];

        foreach ($countries as $c) {
            Country::create($c);
        }

        $this->command->info('Seeding deep data for Pakistan (Priority 1)...');
        $pk = Country::where('name', 'Pakistan')->first();
        if ($pk) {
            $states = [
                'Punjab' => ['Lahore', 'Faisalabad', 'Rawalpindi', 'Gujranwala', 'Multan', 'Sialkot', 'Bahawalpur', 'Sargodha', 'Gujrat', 'Sheikhupura', 'Rahim Yar Khan', 'Jhang', 'Sahiwal', 'Okara', 'Kasur', 'Dera Ghazi Khan', 'Wah Cantonment', 'Chiniot', 'Kamoke', 'Hafizabad', 'Sadiqabad', 'Burewala', 'Khanewal', 'Muzaffargarh', 'Mandi Bahauddin', 'Jhelum', 'Khanpur', 'Pakpattan', 'Vehari', 'Gojra', 'Attock', 'Layyah', 'Taxila', 'Chakwal', 'Bahawalnagar'],
                'Sindh' => ['Karachi', 'Hyderabad', 'Sukkur', 'Larkana', 'Nawabshah', 'Mirpur Khas', 'Jacobabad', 'Shikarpur', 'Khairpur', 'Thatta', 'Badin', 'Ghotki', 'Umerkot'],
                'Khyber Pakhtunkhwa' => ['Peshawar', 'Mardan', 'Mingora', 'Kohat', 'Abbottabad', 'Mansehra', 'Swabi', 'Nowshera', 'Charsadda', 'Dera Ismail Khan', 'Haripur', 'Bannu'],
                'Balochistan' => ['Quetta', 'Turbat', 'Khuzdar', 'Hub', 'Chaman', 'Gwadar', 'Sibi', 'Zhob'],
                'Islamabad Capital Territory' => ['Islamabad'],
                'Azad Kashmir' => ['Muzaffarabad', 'Mirpur', 'Rawalakot', 'Kotli', 'Bhimber'],
                'Gilgit Baltistan' => ['Gilgit', 'Skardu', 'Hunza', 'Diamer']
            ];

            foreach ($states as $stateName => $cities) {
                $s = State::create(['country_id' => $pk->id, 'name' => $stateName]);
                foreach ($cities as $cityName) {
                    City::create(['state_id' => $s->id, 'name' => $cityName]);
                }
            }
        }

        $this->command->info('Location seeding complete! Other countries will fetch states on-demand.');
    }
}
