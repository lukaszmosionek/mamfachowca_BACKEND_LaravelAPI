<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize()
    {
        // return true;
        // Tylko klient może tworzyć rezerwację
        return auth()->check() && auth()->user()->isProvider();
    }

    public function rules()
    {
        return [
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.language.code' => 'required|string|min:1|max:4',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $translations = collect($this->input('translations'));

            $hasAtLeastOne = $translations->contains(function ($translation) {
                return (!empty($translation['name']) || !empty($translation['description']));
            });

            if (! $hasAtLeastOne) {
                $validator->errors()->add('translations', 'At least one name or description is required.');
            }
        });
    }
}
