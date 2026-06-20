<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Subsidiary;
use App\Models\Shift;
use App\Models\Sector;

#[Fillable([
    'first_name',
    'last_name',
    'username',
    'email',
    'phone_number',
    'role',
    'is_active',
    'subsidiary_id',
    'signature_path',
    'password',
    'shift_id',
    'sector_id',
    'is_logged_in',
    'auto_attendance',
    'created_by'
])]

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_logged_in' => 'boolean',
            'auto_attendance' => 'boolean',
        ];
    }
    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }

    public function email(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtolower($value),
        );

    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

}
