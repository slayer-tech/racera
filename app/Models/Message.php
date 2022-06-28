<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'profile_id',
        'content',
        'created_at',
        'updated_at'
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function chat()
    {
        return $this->hasOne(Chat::class);
    }
}
