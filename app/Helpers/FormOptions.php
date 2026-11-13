<?php

namespace App\Helpers;

class FormOptions
{
    public static function maritalStatuses()
    {
        return [
            'Single',
            'Divorced',
            'Widowed',
            'Separated',
            'Annulled',
        ];
    }

    public static function religions()
    {
        return [
            'Islam',
            'Christianity',
            'Hinduism',
            'Sikhism',
            'Buddhism',
            'Judaism',
            'Bahai',
            'Other',
        ];
    }

    public static function sects()
    {
        return [
            'Sunni',
            'Shia',
            'Wahabi',
            'Deobandi',
            'Barelvi',
            'Salafi',
            'Ahle Hadees',
            'Catholic',
            'Protestant',
            'Orthodox',
            'Other',
        ];
    }

    public static function castes()
    {
        return [
            'Jat', 'Rajput', 'Arain', 'Gujjar', 'Syed', 'Pathan', 'Mughal', 
            'Sheikh', 'Butt', 'Malik', 'Awan', 'Qureshi', 'Ansari', 'Khatri', 
            'Khokhar', 'Baloch', 'Abbasi', 'Chaudhry', 'Gardezi', 'Gillani', 
            'Hashmi', 'Kazmi', 'Naqvi', 'Rizvi', 'Zaidi', 'Alvi', 'Farooqi', 
            'Siddiqi', 'Usmani', 'Lodhi', 'Niazi', 'Khattak', 'Yousafzai', 
            'Afridi', 'Wazir', 'Mehsud', 'Bangash', 'Orakzai', 'Marwat', 
            'Durrani', 'Achakzai', 'Kakar', 'Bugti', 'Marri', 'Mengal', 'Rind', 
            'Leghari', 'Khosa', 'Jamali', 'Tarar', 'Virk', 'Warraich', 'Cheema', 
            'Sandhu', 'Gill', 'Bajwa', 'Bhatti', 'Dogar', 'Minhas', 'Kashmiri', 
            'Dar', 'Lone', 'Mir', 'Khan', 'Other'
        ];
    }

    public static function motherTongues()
    {
        return [
            'Urdu', 'Punjabi', 'Sindhi', 'Pashto', 'Balochi', 'Saraiki', 
            'Hindko', 'Kashmiri', 'English', 'Other'
        ];
    }

    public static function educationLevels()
    {
        return [
            'Matric / O-Levels',
            'Intermediate / A-Levels',
            'Bachelors (General)',
            'Bachelors (Engineering)',
            'Bachelors (Medical - MBBS/BDS)',
            'Bachelors (Business/CA/ACCA)',
            'Masters',
            'M.Phil / MS',
            'PhD / Doctorate',
            'Diploma / Certificate',
            'Other',
        ];
    }

    public static function majorsByQualification()
    {
        return [
            'Matric / O-Levels' => ['Science', 'Arts', 'I.Com', 'Other'],
            'Intermediate / A-Levels' => ['FSc Pre-Medical', 'FSc Pre-Engineering', 'ICS (Computer Science)', 'I.Com (Commerce)', 'FA (Arts)', 'Other'],
            'Bachelors (General)' => ['BA', 'BSc', 'B.Com', 'BS-CS', 'BS-IT', 'BS-English', 'BS-Psychology', 'Other'],
            'Bachelors (Engineering)' => ['Software Engineering', 'Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering', 'Chemical Engineering', 'Mechatronics', 'Other'],
            'Bachelors (Medical - MBBS/BDS)' => ['MBBS', 'BDS', 'Pharm-D', 'DPT (Physiotherapy)', 'Nursing', 'Other'],
            'Bachelors (Business/CA/ACCA)' => ['BBA', 'ACCA', 'CA (Inter/Final)', 'ACMA', 'MBA (Executive)', 'Other'],
            'Masters' => ['MA', 'MSc', 'MCS', 'MIT', 'MBA', 'M.Com', 'Other'],
            'M.Phil / MS' => ['MS Computer Science', 'M.Phil Education', 'MS Engineering', 'MS Management', 'Other'],
            'PhD / Doctorate' => ['PhD Computer Science', 'PhD Education', 'PhD Engineering', 'PhD Islamic Studies', 'Other'],
            'Diploma / Certificate' => ['DAE (Associate Engineering)', 'IT Diploma', 'Language Course', 'Other'],
            'Other' => ['Other']
        ];
    }

    public static function professions()
    {
        return [
            'Armed Forces' => [
                'Army Officer', 'Air Force Officer', 'Navy Officer', 
                'Soldier / Airman / Sailor', 'Armed Forces Resident', 'Retired Military'
            ],
            'Government & CSS' => [
                'CSS Officer', 'PMS / Provincial Officer', 'BPS Govt Official', 
                'Govt Teacher / Lecturer', 'Police Officer', 'Custom / FIA Officer',
                'Railway Employee', 'Public Sector Engineer'
            ],
            'Medical & Healthcare' => [
                'Doctor / Specialist', 'Surgeon', 'Dentist', 'Pharmacist', 
                'Nurse', 'Physiotherapist', 'Veterinary Doctor', 'Medical Resident'
            ],
            'Engineering & IT' => [
                'Software Engineer', 'IT Manager', 'Civil Engineer', 
                'Electrical Engineer', 'Mechanical Engineer', 'Architect',
                'Web Developer', 'Graphics Designer', 'Data Scientist'
            ],
            'Finance & Legal' => [
                'Chartered Accountant (CA)', 'Banker', 'Advocate / Lawyer', 
                'Income Tax Practitioner', 'ACCA / Auditor', 'Financial Analyst'
            ],
            'Business & Trade' => [
                'CEO / Director', 'Small Business Owner', 'Trader / Shopkeeper', 
                'Real Estate Agent', 'Impex / Export Business'
            ],
            'Education & Academics' => [
                'University Professor', 'School Principal', 'Private Tutor', 
                'Education Consultant'
            ],
            'Agriculture & Other' => [
                'Landlord / Agriculturist', 'Farmer', 'Freelancer', 
                'Journalist', 'Pilot (Civil)', 'Steward / Cabin Crew',
                'Overseas Job', 'Other'
            ]
        ];
    }

    public static function cities()
    {
         // Top Pakistan cities + International
        return [
            'Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 
            'Peshawar', 'Quetta', 'Gujranwala', 'Sialkot', 'Hyderabad', 'Sukkur', 
            'Bahawalpur', 'Sargodha', 'Gujrat', 'Mardan', 'Kasur', 'Rahim Yar Khan', 
            'Sahiwal', 'Okara', 'Wah Cantt', 'Dera Ghazi Khan', 'Mirpur', 'Muzaffarabad', 
            'Abbottabad', 'Swat', 'Gilgit', 'Skardu', 'Gwadar', 'Dubai', 'London', 
            'New York', 'Toronto', 'Sydney', 'Other'
        ];
    }

    public static function relocationOptions()
    {
        return [
            'No',
            'Within Country',
            'Abroad',
            'Depends',
        ];
    }

    public static function lookingToMarryOptions()
    {
        return [
            'Immediately',
            'Within 6 Months',
            'Within 1 Year',
            'Not Sure',
        ];
    }

    public static function religiousnessLevels()
    {
        return [
            'Very Religious',
            'Religious',
            'Moderate',
            'Not Religious',
        ];
    }

    public static function livingArrangements()
    {
        return [
            'Live With Family',
            'Separate House',
            'Any',
        ];
    }

    public static function complexions()
    {
        return [
            'Very Fair', 'Fair', 'Wheatish', 'Wheatish Brown', 'Dark',
        ];
    }

    public static function bodyTypes()
    {
        return [
            'Slim', 'Athletic', 'Average', 'Heavy',
        ];
    }

    public static function eatingHabits()
    {
        return [
            'Vegetarian', 'Non-Vegetarian', 'Eggetarian',
        ];
    }

    public static function smokingHabits()
    {
        return [
            'No', 'Occasionally', 'Yes',
        ];
    }

    public static function drinkingHabits()
    {
        return [
            'No', 'Occasionally', 'Yes',
        ];
    }

    public static function familyValues()
    {
        return [
            'Orthodox', 'Traditional', 'Moderate', 'Liberal',
        ];
    }

    public static function familyTypes()
    {
        return [
            'Joint Family', 'Nuclear Family',
        ];
    }

    public static function familyStatuses()
    {
        return [
            'Middle Class', 'Upper Middle Class', 'Rich / Affluent',
        ];
    }
    
    public static function disabilityOptions()
    {
        return [
            'None',
            'Minor',
            'Yes',
            'Any',
        ];
    }

    public static function incomeOptions()
    {
        return [
            'Not Earning',
            'Less than 50,000',
            '50,000 - 100,000',
            '100,000 - 200,000',
            '200,000 - 500,000',
            '500,000 - 1,000,000',
            '1,000,000+',
        ];
    }
}
