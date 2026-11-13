<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'target_profile_id',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function targetProfile()
    {
        return $this->belongsTo(Profile::class, 'target_profile_id');
    }
}
