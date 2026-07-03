<?php

namespace App\Http\Requests\Responsavel;

use Illuminate\Foundation\Http\FormRequest;

class StorePassageiroEnderecosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalizarCoord = fn (mixed $valor): ?string => match (true) {
            $valor === null => null,
            trim((string) $valor) === '' => null,
            default => str_replace(',', '.', trim((string) $valor)),
        };

        $this->merge([
            'residencia_estado'    => strtoupper((string) $this->residencia_estado),
            'embarque_estado'      => strtoupper((string) $this->embarque_estado),
            'desembarque_estado'   => strtoupper((string) $this->desembarque_estado),
            'residencia_cep'       => preg_replace('/\D/', '', (string) $this->residencia_cep),
            'embarque_cep'         => preg_replace('/\D/', '', (string) $this->embarque_cep),
            'desembarque_cep'      => preg_replace('/\D/', '', (string) $this->desembarque_cep),
            'residencia_latitude'  => $normalizarCoord($this->residencia_latitude),
            'residencia_longitude' => $normalizarCoord($this->residencia_longitude),
            'embarque_latitude'    => $normalizarCoord($this->embarque_latitude),
            'embarque_longitude'   => $normalizarCoord($this->embarque_longitude),
            'desembarque_latitude' => $normalizarCoord($this->desembarque_latitude),
            'desembarque_longitude'=> $normalizarCoord($this->desembarque_longitude),
        ]);
    }

    public function rules(): array
    {
        $enderecoRules = [
            'logradouro' => ['required', 'string', 'max:150'],
            'numero' => ['nullable', 'string', 'max:10'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'size:2'],
            'cep' => ['required', 'digits:8'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];

        return [
            'residencia_logradouro' => $enderecoRules['logradouro'],
            'residencia_numero' => $enderecoRules['numero'],
            'residencia_complemento' => $enderecoRules['complemento'],
            'residencia_bairro' => $enderecoRules['bairro'],
            'residencia_cidade' => $enderecoRules['cidade'],
            'residencia_estado' => $enderecoRules['estado'],
            'residencia_cep' => $enderecoRules['cep'],
            'residencia_latitude' => $enderecoRules['latitude'],
            'residencia_longitude' => $enderecoRules['longitude'],

            'embarque_logradouro' => $enderecoRules['logradouro'],
            'embarque_numero' => $enderecoRules['numero'],
            'embarque_complemento' => $enderecoRules['complemento'],
            'embarque_bairro' => $enderecoRules['bairro'],
            'embarque_cidade' => $enderecoRules['cidade'],
            'embarque_estado' => $enderecoRules['estado'],
            'embarque_cep' => $enderecoRules['cep'],
            'embarque_latitude' => $enderecoRules['latitude'],
            'embarque_longitude' => $enderecoRules['longitude'],

            'desembarque_logradouro' => $enderecoRules['logradouro'],
            'desembarque_numero' => $enderecoRules['numero'],
            'desembarque_complemento' => $enderecoRules['complemento'],
            'desembarque_bairro' => $enderecoRules['bairro'],
            'desembarque_cidade' => $enderecoRules['cidade'],
            'desembarque_estado' => $enderecoRules['estado'],
            'desembarque_cep' => $enderecoRules['cep'],
            'desembarque_latitude' => $enderecoRules['latitude'],
            'desembarque_longitude' => $enderecoRules['longitude'],

            'desembarque_nome' => ['nullable', 'string', 'max:150'],
        ];
    }
}
