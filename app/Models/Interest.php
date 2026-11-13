<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_profile_id',
        'to_profile_id',
        'status',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function fromProfile()
    {
        return $this->belongsTo(Profile::class, 'from_profile_id');
    }

    public function toProfile()
    {
        return $this->belongsTo(Profile::class, 'to_profile_id');
    }
}
