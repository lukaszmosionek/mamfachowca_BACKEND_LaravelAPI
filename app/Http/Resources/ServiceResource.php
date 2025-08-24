<?php

namespace App\Http\Resources;

use App\Models\ServiceTranslation;
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
        // Use the requested locale or fallback to app locale or 'en'
        $locale = $request->get('locale', app()->getLocale());
        // $locale = app()->getLocale();

        // Get the translation for that locale
        $translation = $this->translations->firstWhere('language.code', $locale)
                    ?? $this->translations->firstWhere('language.code', 'en');

        return [
            'id'              => $this->id,
            // 'user_id'        => $this->user_id,
            'name'            => $translation ? $translation->name : null,
            'description'     => $translation ? $translation->description : null,
            'translations'    => ServiceTranslationResource::collection($this->whenLoaded('translations')),
            'price'           => rtrim(rtrim(number_format($this->price, 2, '.', ''), '0'), '.'), // delete unnecesery .00 from e.g. 12.00 price
            'duration_minutes'=> $this->duration_minutes,
            'is_favorited'    => $this->is_favorited,
            'currency'        => new CurrencyResource($this->whenLoaded('currency')),
            'provider'        => new UserResource($this->whenLoaded('provider')),
            'photos'          => PhotoResource::collection($this->whenLoaded('photos')),
            // 'created_at'     => $this->created_at,
            // 'updated_at'     => $this->updated_at,
        ];
    }
}
