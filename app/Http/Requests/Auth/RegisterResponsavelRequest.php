<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterResponsavelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf'      => preg_replace('/\D/', '', $this->cpf ?? ''),
            'email'    => strtolower($this->email ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome'            => ['required', 'string', 'min:2', 'max:150'],
            'cpf'             => ['required', 'cpf', Rule::unique('pessoa', 'cpf')],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'telefone'        => [
                'required',
                'celular_com_ddd',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $telefoneNumerico = preg_replace('/\D/', '', (string) $value);

                    if (DB::table('pessoa')->where('telefone', $telefoneNumerico)->exists()) {
                        $fail('O valor indicado para o campo :attribute já se encontra registrado.');
                    }
                },
            ],
            'email'           => ['required', 'email:rfc,dns', 'max:150', Rule::unique('usuario', 'email')],
            'password'        => ['required', 'confirmed', Password::defaults()],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'telefone' => preg_replace('/\D/', '', (string) $this->telefone),
        ]);
    }
}
