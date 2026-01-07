<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Portugal', 'code' => 'PT', 'active' => true],
            ['name' => 'Espanha', 'code' => 'ES', 'active' => true],
            ['name' => 'França', 'code' => 'FR', 'active' => true],
            ['name' => 'Alemanha', 'code' => 'DE', 'active' => true],
            ['name' => 'Itália', 'code' => 'IT', 'active' => true],
            ['name' => 'Reino Unido', 'code' => 'GB', 'active' => true],
            ['name' => 'Países Baixos', 'code' => 'NL', 'active' => true],
            ['name' => 'Bélgica', 'code' => 'BE', 'active' => true],
            ['name' => 'Suíça', 'code' => 'CH', 'active' => true],
            ['name' => 'Áustria', 'code' => 'AT', 'active' => true],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
