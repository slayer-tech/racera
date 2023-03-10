<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'description',
        'creator_id'
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
