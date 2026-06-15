<?php

namespace App\Http\Requests\Responsavel;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePassageiroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'telefone' => $this->telefone
                ? preg_replace('/\D/', '', $this->telefone)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nome'            => ['required', 'string', 'min:2', 'max:150'],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'telefone'        => ['nullable', 'celular_com_ddd'],
            'obs_medica'      => ['nullable', 'string', 'max:5000'],
        ];
    }
}