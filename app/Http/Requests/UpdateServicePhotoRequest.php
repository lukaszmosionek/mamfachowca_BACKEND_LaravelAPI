<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class UpdateServicePhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $service = $this->route('service'); // message failedAuthorization()
        return $service && auth()->user()->can('update', $service);  //class ServicePolicy update()
    }

    public function rules(): array
    {
        return [
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You are not allowed to update this service.');
    }
}
