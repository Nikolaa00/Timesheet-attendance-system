<?php

namespace App\Http\Resources;

use App\Support\ApiRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'username' => $this->username,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'subsidiary' => $this->relationLoaded('subsidiary') && $this->subsidiary
                ? ['id' => $this->subsidiary->id, 'name' => $this->subsidiary->name]
                : null,
            'sector' => $this->relationLoaded('sector') && $this->sector
                ? ['id' => $this->sector->id, 'name' => $this->sector->name]
                : null,
            'shift_id' => $this->shift_id,
            'role' => ApiRole::toApi($this->role),
            'auto_attendance' => $this->auto_attendance,
        ];
    }
}
