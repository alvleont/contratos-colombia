<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ColombianStateApi
{
    private const BASE_URL = 'https://www.datos.gov.co/resource/rpmr-utcd.json';

    public function getContractsBySupplier(string $document)
    {
        return Cache::remember(
            "supplier.document.{$document}",
            now()->addHour(),
            fn () => $this->fetchFromApi(['documento_proveedor' => $document])
        );
    }

    public function getContractsByName(string $name)
    {
        return Cache::remember(
            "supplier.name." . md5($name),
            now()->addHour(),
            fn () => $this->fetchFromApi(['$where' => "lower(nom_raz_social_contratista) like lower('%{$name}%')"]) // Usando SOQL para búsqueda case-insensitive
        );
    }

    private function fetchFromApi(array $params)
    {
        try {
            $response = Http::get(self::BASE_URL, $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Error en la respuesta de la API: ' . $response->status());
        } catch (\Exception $e) {
            \Log::error('Error consultando la API: ' . $e->getMessage());
            throw $e;
        }
    }
}
