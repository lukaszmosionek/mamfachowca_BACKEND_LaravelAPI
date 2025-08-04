<?php

namespace App\Http\Resources;

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
            'id'                => $this->id,
            'name'              => $this->name,
            'availabilities'    => AvailabilityResource::collection($this->whenLoaded('availabilities')),
            'email'             => $this->email,
            // 'email_verified_at' => $this->email_verified_at,
            'role'              => $this->role,
            'avatar'              => $this->avatar ? config('app.url').'/'.$this->avatar : null,
            // 'created_at'        => $this->created_at,
            // 'updated_at'        => $this->updated_at,
        ];
    }
}
