<?php

namespace App\Http\Resources;

use App\Support\ApiRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
            'role' => ApiRole::toApi($this->role),
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];
    }
}
