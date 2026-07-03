<?php

namespace App\Http\Requests\Responsavel;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePassageiroEnderecosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enderecoRules = [
            'logradouro'  => ['required', 'string', 'max:150'],
            'numero'      => ['nullable', 'string', 'max:10'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro'      => ['required', 'string', 'max:100'],
            'cidade'      => ['required', 'string', 'max:100'],
            'estado'      => ['required', 'string', 'size:2'],
            'cep'         => ['required', 'digits:8'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
        ];

        return [
            // Múltiplos embarques
            'embarques'                => ['nullable', 'array'],
            'embarques.*.logradouro'   => $enderecoRules['logradouro'],
            'embarques.*.numero'       => $enderecoRules['numero'],
            'embarques.*.complemento'  => $enderecoRules['complemento'],
            'embarques.*.bairro'       => $enderecoRules['bairro'],
            'embarques.*.cidade'       => $enderecoRules['cidade'],
            'embarques.*.estado'       => $enderecoRules['estado'],
            'embarques.*.cep'          => $enderecoRules['cep'],
            'embarques.*.latitude'     => $enderecoRules['latitude'],
            'embarques.*.longitude'    => $enderecoRules['longitude'],

            // Múltiplos desembarques
            'desembarques'               => ['nullable', 'array'],
            'desembarques.*.nome'        => ['nullable', 'string', 'max:150'],
            'desembarques.*.logradouro'  => $enderecoRules['logradouro'],
            'desembarques.*.numero'      => $enderecoRules['numero'],
            'desembarques.*.complemento' => $enderecoRules['complemento'],
            'desembarques.*.bairro'      => $enderecoRules['bairro'],
            'desembarques.*.cidade'      => $enderecoRules['cidade'],
            'desembarques.*.estado'      => $enderecoRules['estado'],
            'desembarques.*.cep'         => $enderecoRules['cep'],
            'desembarques.*.latitude'    => $enderecoRules['latitude'],
            'desembarques.*.longitude'   => $enderecoRules['longitude'],

            // Residência (única)
            'residencia'              => ['nullable', 'array'],
            'residencia.logradouro'   => ['nullable', 'string', 'max:150'],
            'residencia.numero'       => $enderecoRules['numero'],
            'residencia.complemento'  => $enderecoRules['complemento'],
            'residencia.bairro'       => ['nullable', 'string', 'max:100'],
            'residencia.cidade'       => ['nullable', 'string', 'max:100'],
            'residencia.estado'       => ['nullable', 'string', 'size:2'],
            'residencia.cep'          => ['nullable', 'digits:8'],
            'residencia.latitude'     => $enderecoRules['latitude'],
            'residencia.longitude'    => $enderecoRules['longitude'],

        ];
    }
}