<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upgrade extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function bonuses()
    {
        return $this->hasOne(Bonus::class);
    }

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'upgrade_profile');
    }
}
