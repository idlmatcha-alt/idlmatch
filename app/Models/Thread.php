<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_a_id',
        'profile_b_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function profileA()
    {
        return $this->belongsTo(Profile::class, 'profile_a_id');
    }

    public function profileB()
    {
        return $this->belongsTo(Profile::class, 'profile_b_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }
}
