<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'user_id' => $this->user_id,
            'check_in_time' => $this->check_in_time?->toISOString(),
            'check_out_time' => $this->check_out_time?->toISOString(),
            'auto_checkout' => (bool) $this->auto_check_out,
        ];
    }
}
