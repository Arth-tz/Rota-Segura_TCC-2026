<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
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
            'email'    => strtolower($this->email ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome'            => ['required', 'string', 'min:2', 'max:150'],
            'cpf'             => ['required', 'cpf', Rule::unique('pessoa', 'cpf')],
            'data_nascimento' => ['required', 'date', 'before_or_equal:' . now()->subYears(21)->format('Y-m-d')],
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
            'cnh_numero'      => ['required', 'digits:11', Rule::unique('motorista', 'cnh_numero')],
            'cnh_categoria'   => ['required', 'in:D,E'],
            'cnh_validade'    => ['required', 'date', 'after:today'],
            'foto'            => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ];
    }

    public function messages(): array
    {
        return [
            'data_nascimento.before_or_equal' => 'O motorista precisa ter pelo menos 21 anos (art. 138, I do CTB).',
            'foto.image' => 'O arquivo deve ser uma imagem.',
            'foto.max'   => 'A imagem deve ter no máximo 2 MB.',
            'foto.mimes' => 'Formato aceito: JPG, PNG ou WebP.',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'telefone' => preg_replace('/\D/', '', (string) $this->telefone),
        ]);
    }
}
