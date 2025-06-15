<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize()
{
    return $this->user()->can('update', $this->route('profile'));
}

public function rules()
{
    return [
        'birthdate' => 'sometimes|nullable|date',
        'address'   => 'sometimes|nullable|string|max:255',
    ];
}

}
