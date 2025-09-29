<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreServiceRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->isProvider();
    }

    public function rules()
    {
        return [
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.language.code' => 'required|string|min:1|max:4',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|numeric|min:1|exists:currencies,id',
            'duration_minutes' => 'required|integer|min:1',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Convert flat dot notation to nested array
        $nestedErrors = Arr::undot($validator->errors()->toArray());

        throw new HttpResponseException(
            response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => $nestedErrors,
            ], 422)
        );
    }
}
