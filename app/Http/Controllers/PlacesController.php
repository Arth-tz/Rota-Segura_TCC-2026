<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlacesController extends Controller
{
    private string $key;

    public function __construct()
    {
        $this->key = config('services.geoapify.key');
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $input = $request->query('q', '');
        if (strlen($input) < 3) return response()->json([]);

        $res = Http::timeout(5)->get('https://api.geoapify.com/v1/geocode/autocomplete', [
            'text'   => $input,
            'filter' => 'countrycode:br',
            'lang'   => 'pt',
            'limit'  => 6,
            'apiKey' => $this->key,
        ]);

        if (!$res->ok()) return response()->json([]);

        $features = $res->json('features') ?? [];

        return response()->json(array_values(array_map(function ($f) {
            $p = $f['properties'] ?? [];

            $cep = preg_replace('/\D/', '', $p['postcode'] ?? '');
            if (strlen($cep) === 8) {
                $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);
            }

            return [
                'place_id'    => $p['place_id'] ?? uniqid(),
                'description' => $p['formatted'] ?? '',
                'logradouro'  => $p['street']      ?? '',
                'numero'      => $p['housenumber']  ?? '',
                'bairro'      => $p['suburb'] ?? $p['district'] ?? $p['neighbourhood'] ?? '',
                'cidade'      => $p['city']    ?? $p['town']   ?? $p['village'] ?? '',
                'estado'      => $p['state_code']  ?? '',
                'cep'         => $cep,
                'latitude'    => $f['geometry']['coordinates'][1] ?? '',
                'longitude'   => $f['geometry']['coordinates'][0] ?? '',
            ];
        }, $features)));
    }

    // Mantido para compatibilidade mas não é mais chamado pelo frontend
    public function details(Request $request): JsonResponse
    {
        return response()->json(null);
    }
}
