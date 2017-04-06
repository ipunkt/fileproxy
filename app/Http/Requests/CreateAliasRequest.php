<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAliasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'path' => 'required|min:6|max:'.MAX_STRING_LENGTH.'|unique:file_aliases,path',
            'hits' => 'numeric|min:0',
            'from' => 'sometimes|nullable|date',
            'until' => 'sometimes|nullable|date',
        ];
    }
}
