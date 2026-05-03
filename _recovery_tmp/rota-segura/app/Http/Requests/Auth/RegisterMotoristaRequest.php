<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterMotoristaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf'      => preg_replace('/\D/', '', $this->cpf ?? ''),
            'telefone' => preg_replace('/\D/', '', $this->telefone ?? ''),
            'email'    => strtolower($this->email ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome'            => ['required', 'string', 'min:2', 'max:150'],
            'cpf'             => ['required', 'cpf', Rule::unique('pessoa', 'cpf')],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'telefone'        => ['required', 'celular_com_ddd', Rule::unique('pessoa', 'telefone')],
            'email'           => ['required', 'email:rfc,dns', 'max:150', Rule::unique('usuario', 'email')],
            'password'        => ['required', 'confirmed', Password::defaults()],
            'cnh_numero'      => ['required', 'digits:11', Rule::unique('motorista', 'cnh_numero')],
            'cnh_categoria'   => ['required', 'in:D,E'],
            'cnh_validade'    => ['required', 'date', 'after:today'],
        ];
    }
}
