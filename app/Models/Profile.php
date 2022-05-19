<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'privilege',
        'avatar',
        'clan_id'
    ];

    public function user()
    {
        $this->hasOne(User::class, 'id');
    }

    public function clan()
    {
        $this->hasOne(Clan::class);
    }
}
