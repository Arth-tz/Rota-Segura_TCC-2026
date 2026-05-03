<?php

namespace App\Http\Requests\Responsavel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePassageiroEssencialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => preg_replace('/\D/', '', (string) $this->cpf),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:2', 'max:150'],
            'cpf' => ['required', 'cpf', Rule::unique('pessoa', 'cpf')],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'obs_medica' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

