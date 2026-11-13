<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'name',
        'filters_json',
    ];

    protected $casts = [
        'filters_json' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
