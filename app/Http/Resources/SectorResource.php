<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectorResource extends JsonResource
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
            'name' => $this->name,
            'subsidiaries' => $this->whenLoaded('subsidiaries', function () {
                return $this->subsidiaries->map(fn($subsidiary): array => [
                    'id' => $subsidiary->id,
                    'name' => $subsidiary->name,
                ])->values();
            }),
            'employee_count' => $this->whenCounted('users'),
            'shifts_count' => $this->shifts_count,
        ];
    }
}
