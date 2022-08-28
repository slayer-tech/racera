<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function upgrades()
    {
        return $this->hasMany(Upgrade::class);
    }

    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }
}
