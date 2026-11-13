<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'blocker_profile_id',
        'blocked_profile_id',
    ];

    public function blocker()
    {
        return $this->belongsTo(Profile::class, 'blocker_profile_id');
    }

    public function blocked()
    {
        return $this->belongsTo(Profile::class, 'blocked_profile_id');
    }
}
