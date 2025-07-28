<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            // 'user_id'        => $this->user_id,
            'provider'       => new UserResource($this->whenLoaded('provider')),
            'photos'         => PhotoResource::collection($this->whenLoaded('photos')),
            'name'           => $this->name,
            'description'    => $this->description,
            'price'          => rtrim(rtrim(number_format($this->price, 2, '.', ''), '0'), '.'), // delete unnecesery .00 from e.g. 12.00 price
            'duration'       => $this->duration_minutes,
            // 'created_at'     => $this->created_at,
            // 'updated_at'     => $this->updated_at,
        ];
    }
}
