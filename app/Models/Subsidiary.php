<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subsidiary extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'email',
        'parent_company_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function parentCompany()
    {
        return $this->belongsTo(Subsidiary::class, "parent_company_id");
    }

    public function children()
    {
        return $this->hasMany(Subsidiary::class, "parent_company_id");
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class)->withTimestamps();
    }

    public function allUsers()
    {
        return $this->hasMany(User::class);
    }

    public function isDescendant(int $id): bool
    {
        foreach ($this->children as $child) {
            if ($child->id === $id || $child->isDescendant($id)) {
                return true;
            }
        }
        return false;
    }
}
