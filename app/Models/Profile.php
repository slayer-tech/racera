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
        'clan_id',
        'money',
        'fuel',
        'wins',
        'loses'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function clan()
    {
        return $this->hasOne(Clan::class);
    }

    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }

    public function upgrades()
    {
        return $this->belongsToMany(Upgrade::class, 'upgrade_profile');
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class);
    }

    public function privilege() {
        return $this->belongsTo(Privilege::class);
    }

    public function games() {
        return $this->belongsTo(Game::class);
    }
}
