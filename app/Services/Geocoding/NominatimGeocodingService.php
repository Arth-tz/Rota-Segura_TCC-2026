<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\Http;

class NominatimGeocodingService
{
    public function geocodeAddress(array $endereco): ?array
    {
        $query = $this->buildQuery($endereco);

        if ($query === null) {
            return null;
        }

        $baseUrl = rtrim((string) config('services.nominatim.base_url', 'https://nominatim.openstreetmap.org'), '/');
        $timeout = (int) config('services.nominatim.timeout', 5);
        $userAgent = (string) config('services.nominatim.user_agent', config('app.name', 'RotaSegura') . '/1.0');
        $email = (string) config('services.nominatim.email', '');

        $params = [
            'q' => $query,
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 0,
            'countrycodes' => 'br',
        ];

        if ($email !== '') {
            $params['email'] = $email;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => $userAgent,
                'Accept-Language' => 'pt-BR',
            ])->timeout($timeout)->get($baseUrl . '/search', $params);

            if (!$response->ok()) {
                return null;
            }

            $data = $response->json();

            if (!is_array($data) || empty($data[0]['lat']) || empty($data[0]['lon'])) {
                return null;
            }

            return [
                'latitude' => (string) $data[0]['lat'],
                'longitude' => (string) $data[0]['lon'],
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function buildQuery(array $endereco): ?string
    {
        $parts = [
            trim((string) ($endereco['logradouro'] ?? '')),
            trim((string) ($endereco['numero'] ?? '')),
            trim((string) ($endereco['bairro'] ?? '')),
            trim((string) ($endereco['cidade'] ?? '')),
            trim((string) ($endereco['estado'] ?? '')),
            trim((string) ($endereco['cep'] ?? '')),
            'Brasil',
        ];

        $parts = array_values(array_filter($parts, static fn (string $part) => $part !== ''));

        if (count($parts) < 3) {
            return null;
        }

        return implode(', ', $parts);
    }
}

