<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'The text body cannot be empty',
        ];
    }
}
