<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        // سمحنا لكل مستخدم مصادق له بعمل هذا الطلب
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'supervisor'   => 'required|string|max:255',
            'submitted_at' => 'required|date',
'pdf' => 'required|file|mimes:pdf|max:10240',
            'description'  => 'nullable|string',
        ];
    }
}
