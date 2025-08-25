<?php

namespace App\Http\Requests;

use App\Enum\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'role' => [  Rule::in(Role::valuesExceptAdmin()) ],
        ];
    }
}
