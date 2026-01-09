<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ViesService
{
    /**
     * Validate VAT number and get company data from VIES.
     *
     * @param string $countryCode Two-letter country code (e.g., 'PT', 'ES')
     * @param string $vatNumber VAT number without country code
     * @return array|null
     */
    public function validate(string $countryCode, string $vatNumber): ?array
    {
        try {
            // Clean the VAT number
            $vatNumber = preg_replace('/[^0-9A-Z]/i', '', $vatNumber);
            $countryCode = strtoupper($countryCode);

            // Make request to VIES API
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->get('https://ec.europa.eu/taxation_customs/vies/rest-api/ms/' . $countryCode . '/vat/' . $vatNumber);

            // Check if request was successful
            if ($response->ok() && $response->status() === 200) {
                $data = $response->json() ?? [];

                // Check if VAT is valid
                if (isset($data['isValid']) && $data['isValid'] === true) {
                    return [
                        'valid' => true,
                        'country_code' => $data['countryCode'] ?? $countryCode,
                        'vat_number' => $data['vatNumber'] ?? $vatNumber,
                        'name' => $data['name'] ?? null,
                        'address' => $data['address'] ?? null,
                    ];
                }

                return [
                    'valid' => false,
                    'error' => 'VAT number is not valid',
                ];
            }

            // If API returns error
            return [
                'valid' => false,
                'error' => 'Could not validate VAT number',
            ];
        } catch (\Exception $e) {
            Log::error('VIES validation error: ' . $e->getMessage());

            return [
                'valid' => false,
                'error' => 'Service temporarily unavailable',
            ];
        }
    }

    /**
     * Extract country code from NIF.
     *
     * @param string $nif
     * @return array ['country_code' => string, 'vat_number' => string]
     */
    public function extractCountryCode(string $nif): array
    {
        // Remove spaces and special characters
        $nif = preg_replace('/[^0-9A-Z]/i', '', strtoupper($nif));

        // Check if starts with country code
        $countryCode = substr($nif, 0, 2);

        // List of EU country codes
        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
            'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT',
            'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
        ];

        if (in_array($countryCode, $euCountries)) {
            return [
                'country_code' => $countryCode,
                'vat_number' => substr($nif, 2),
            ];
        }

        // Default to Portugal if no country code
        return [
            'country_code' => 'PT',
            'vat_number' => $nif,
        ];
    }
}
