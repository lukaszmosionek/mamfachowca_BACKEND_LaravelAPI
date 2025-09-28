<?php

namespace App\Http\Resources;

use App\Models\ServiceTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserServiceResource extends JsonResource
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
            'translations'    => ServiceTranslationResource::collection($this->whenLoaded('translations')),
            'price'          => rtrim(rtrim(number_format($this->price, 2, '.', ''), '0'), '.'), // delete unnecesery .00 from e.g. 12.00 price
            'duration'       => $this->duration_minutes,
            'is_favorited'   => $this->is_favorited,
            'currency'       => new CurrencyResource($this->whenLoaded('currency')),
            'provider'       => new UserResource($this->whenLoaded('provider')),
            'photos'         => PhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
