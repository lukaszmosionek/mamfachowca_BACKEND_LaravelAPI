<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
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
            'is_main' => $this->is_main,

            'thumbnail' => config('app.url').'/'.$this->thumbnail,
            'medium' => config('app.url').'/'.$this->medium,
            'large' => config('app.url').'/'.$this->large
        ];
    }
}
