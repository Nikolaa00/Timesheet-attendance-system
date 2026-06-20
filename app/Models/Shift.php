<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ShiftType;

class Shift extends Model
{
    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    protected function casts(): array
    {
        return [
            'name' => ShiftType::class,
        ];
    }
}


