<?php

namespace Tests\Unit;

use App\Services\ViesService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ViesServiceTest extends TestCase
{
    protected ViesService $viesService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->viesService = new ViesService();
    }

    public function test_it_validates_valid_eu_vat_number()
    {
        Http::fake([
            'ec.europa.eu/*' => Http::response([
                'isValid' => true,
                'countryCode' => 'PT',
                'vatNumber' => '123456789',
                'name' => 'Test Company Lda',
                'address' => 'Rua Test, 123, Lisboa',
            ], 200),
        ]);

        $result = $this->viesService->validate('PT', '123456789');

        $this->assertTrue($result['valid']);
        $this->assertEquals('PT', $result['country_code']);
        $this->assertEquals('123456789', $result['vat_number']);
        $this->assertEquals('Test Company Lda', $result['name']);
        $this->assertEquals('Rua Test, 123, Lisboa', $result['address']);
    }

    public function test_it_returns_false_for_invalid_vat_number()
    {
        Http::fake([
            'ec.europa.eu/*' => Http::response([
                'isValid' => false,
            ], 200),
        ]);

        $result = $this->viesService->validate('PT', '999999999');

        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('VAT number is not valid', $result['error']);
    }

    public function test_it_handles_api_errors()
    {
        Http::fake([
            'ec.europa.eu/*' => Http::response([], 500),
        ]);

        $result = $this->viesService->validate('PT', '123456789');

        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Could not validate VAT number', $result['error']);
    }

    public function test_it_handles_timeout_errors()
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
        });

        $result = $this->viesService->validate('PT', '123456789');

        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Service temporarily unavailable', $result['error']);
    }

    public function test_it_cleans_vat_number_before_validation()
    {
        Http::fake([
            'ec.europa.eu/*' => Http::response([
                'isValid' => true,
                'countryCode' => 'PT',
                'vatNumber' => '123456789',
                'name' => 'Test Company',
                'address' => 'Test Address',
            ], 200),
        ]);

        // Test with spaces and special characters
        $result = $this->viesService->validate('PT', '123 456 789');

        $this->assertTrue($result['valid']);

        // Verify the request was made with cleaned VAT number
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '123456789');
        });
    }

    public function test_it_extracts_country_code_from_eu_vat_number()
    {
        $result = $this->viesService->extractCountryCode('PT123456789');

        $this->assertEquals('PT', $result['country_code']);
        $this->assertEquals('123456789', $result['vat_number']);
    }

    public function test_it_extracts_country_code_from_spanish_vat_number()
    {
        $result = $this->viesService->extractCountryCode('ESX12345678');

        $this->assertEquals('ES', $result['country_code']);
        $this->assertEquals('X12345678', $result['vat_number']);
    }

    public function test_it_defaults_to_portugal_for_vat_without_country_code()
    {
        $result = $this->viesService->extractCountryCode('123456789');

        $this->assertEquals('PT', $result['country_code']);
        $this->assertEquals('123456789', $result['vat_number']);
    }

    public function test_it_handles_vat_number_with_spaces()
    {
        $result = $this->viesService->extractCountryCode('PT 123 456 789');

        $this->assertEquals('PT', $result['country_code']);
        $this->assertEquals('123456789', $result['vat_number']);
    }

    public function test_it_recognizes_all_eu_country_codes()
    {
        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
            'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT',
            'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
        ];

        foreach ($euCountries as $country) {
            $result = $this->viesService->extractCountryCode($country . '123456789');

            $this->assertEquals($country, $result['country_code'], "Failed for country: $country");
            $this->assertEquals('123456789', $result['vat_number']);
        }
    }

    public function test_it_handles_lowercase_country_codes()
    {
        $result = $this->viesService->extractCountryCode('pt123456789');

        $this->assertEquals('PT', $result['country_code']);
        $this->assertEquals('123456789', $result['vat_number']);
    }
}
