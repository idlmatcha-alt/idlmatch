<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'desired_age_min',
        'desired_age_max',
        'desired_height_min',
        'desired_height_max',
        'desired_country',
        'desired_state',
        'desired_countries',
        'desired_cities',
        'desired_mother_tongue',
        'desired_sects',
        'desired_religion',
        'desired_sect',
        'desired_education',
        'desired_profession',
        'desired_professions',
        'desired_marital_statuses',
        'desired_caste',
        'desired_marital_status',
        'desired_city',
        'desired_religiousness',
        'desired_willing_to_relocate',
        'desired_living_arrangement',
        'desired_income',
        'desired_disability',
        'desired_complexion',
        'desired_body_type',
        'desired_family_values',
        'desired_eating_habits',
        'desired_smoking_habits',
        'other_requirements',
    ];

    protected $casts = [
        'desired_countries' => 'array',
        'desired_cities' => 'array',
        'desired_sects' => 'array',
        'desired_education' => 'array',
        'desired_professions' => 'array',
        'desired_marital_statuses' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
