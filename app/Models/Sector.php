<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = [
        'name',
    ];

    public function subsidiaries()
    {
        return $this->belongsToMany(Subsidiary::class)->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
