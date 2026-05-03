<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:2', 'max:150'],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:150',
                Rule::unique('usuario', 'email')->ignore($this->user()?->id_usuario, 'id_usuario'),
            ],
        ];
    }
}
