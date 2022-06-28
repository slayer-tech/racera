<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    public function upgrades()
    {
        return $this->belongsToMany(Upgrade::class);
    }

    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }
}
