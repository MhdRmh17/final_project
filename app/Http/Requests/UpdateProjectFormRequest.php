<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize()
{
    // 1) إما تسمح لكلّ من يمرّ منهج auth:sanctum (وتمرّله سياسة update بعد ذلك):
    return true;

    // 2) أو تربطه مباشرة بالسياسة:
    // return $this->user()->can('update', $this->route('project_form'));
}


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules()
{
    return [
        'title'        => 'sometimes|string|max:255',
        'supervisor'   => 'sometimes|string|max:255',
        'submitted_at' => 'sometimes|date',
        'pdf'          => 'sometimes|file|mimes:pdf|max:10240',
        'description'  => 'sometimes|nullable|string',
    ];
}

}
