<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    public function profiles()
    {
        return $this->belongsToMany(Profile::class);
    }

    public function bonuses()
    {
        return $this->belongsToMany(Bonus::class);
    }
}
