<?php

namespace Database\Seeders;

use App\Models\VatRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VatRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vatRates = [
            ['name' => 'IVA Normal (23%)', 'rate' => 23.00, 'active' => true],
            ['name' => 'IVA Reduzido (13%)', 'rate' => 13.00, 'active' => true],
            ['name' => 'IVA Intermédio (6%)', 'rate' => 6.00, 'active' => true],
            ['name' => 'Isento', 'rate' => 0.00, 'active' => true],
        ];

        foreach ($vatRates as $vatRate) {
            VatRate::create($vatRate);
        }
    }
}
