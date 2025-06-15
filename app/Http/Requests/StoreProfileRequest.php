<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize()
{
    return $this->user()->can('create', Profile::class);
}

public function rules()
{
    return [
        'user_id'   => 'required|exists:users,id|unique:profiles,user_id',
        'birthdate' => 'nullable|date',
        'address'   => 'nullable|string|max:255',
    ];
}

}
