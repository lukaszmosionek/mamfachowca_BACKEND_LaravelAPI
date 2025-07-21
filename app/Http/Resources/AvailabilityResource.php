<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            // 'provider_id' => $this->provider_id,
            'day_of_week' => $this->day_of_week,
            'day_of_week_number' => date('N', strtotime($this->day_of_week)),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            // 'created_at' => $this->created_at?->toDateTimeString(),
            // 'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
