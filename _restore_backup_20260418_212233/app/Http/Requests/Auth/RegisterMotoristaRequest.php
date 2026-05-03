<?php

namespace App\Http\Requests\Auth;

use App\Models\Motorista;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterMotoristaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
    public function authorize(): bool
    {
        return true;
    }

     protected function prepareForValidation()
    {
        $this->merge([
            'cpf'      => preg_replace('/\D/', '', $this->cpf),// Remove pontos e traços
            //'phone' => preg_replace('/\D/', '', $this->phone),// Remove ( ) e -
            'email'    => strtolower($this->email),// Padroniza para minúsculo
        ]);
    }
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:100'],

            'last_name' => ['required', 'string'],

            'cpf' => [
                'required', 'cpf', Rule::unique('users', 'cpf'),
            ],

            'phone' => [
                'required', 'celular_com_ddd',
                Rule::unique( 'users', 'phone'),
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers() 
                    ->uncompromised(),
            ],
            
            'cnh_numero' => [
                'required', 
                'string',
                'digits:11', 
                Rule::unique(Motorista::class, 'cnh_numero')
            ],

            'cnh_categoria' => ['required', 'string', 'in:D,E'],

            'cnh_validade' => [
                'required',
                'date',
                'after:today'
            ]

        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'O nome é um campo obrigatório!',
            'last_name.required' => 'O sobrenome é um campo obrigatório!',
            'cpf.cpf'   => 'O CPF informado não é válido!',
            'cpf.unique' => 'Este CPF já está em uso no sistema!',
            'phone.celular_com_ddd' => 'O telefone informado não é válido!',
            'email.email'        => 'Por favor, insira um e-mail válido.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.uncompromised' => 'Esta senha apareceu em um vazamento de dados. Escolha outra segurança.',
            'cnh_numero.required' => 'O número da CNH é obrigatório.',
            'cnh_numero.digits'   => 'A CNH deve conter 11 dígitos.',
            'cnh_numero.unique'   => 'Esta CNH já está cadastrada.',
            'cnh_categoria.in'    => 'A categoria deve ser D ou E.',
            'cnh_validade.after'  => 'A CNH não pode estar vencida.'

        ];
    }
}
