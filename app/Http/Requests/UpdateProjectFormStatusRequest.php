<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
public function authorize(): bool
{
    return true;
}


    public function rules(): array
    {
        return [
            'name'      => 'sometimes|required|string|max:255',
            'phone'     => 'sometimes|nullable|string|max:20',
            'birthdate' => 'sometimes|nullable|date',
            'address'   => 'sometimes|nullable|string|max:255',
        ];
    }
}
