<?php

namespace App\Http\Resources;

use App\Models\Photo;
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
        $sizes = [];
        foreach(Photo::getSizeKeys() as $name){
            $sizes[$name] = $this->{$name} ? Photo::getUrl($this->{$name}) : Photo::getUrl($this->original);
        }

        return [
            'id' => $this->id,
            'is_main' => $this->is_main
        ]+$sizes;
    }
}
