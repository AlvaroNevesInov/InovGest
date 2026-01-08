<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\VatRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing VAT rates
        $vatRates = VatRate::all();

        if ($vatRates->isEmpty()) {
            $this->command->warn('No VAT rates found. Please run VatRateSeeder first.');
            return;
        }

        $articles = [
            [
                'reference' => 'ART-00001',
                'name' => 'Computador Portátil Dell Latitude',
                'description' => 'Portátil profissional com processador Intel i7, 16GB RAM, SSD 512GB',
                'price' => 899.99,
                'vat_rate_id' => $vatRates->where('rate', 23)->first()->id,
                'notes' => 'Inclui garantia de 3 anos',
                'active' => true,
            ],
            [
                'reference' => 'ART-00002',
                'name' => 'Monitor LG 27" 4K',
                'description' => 'Monitor profissional 27 polegadas com resolução 4K UHD',
                'price' => 349.99,
                'vat_rate_id' => $vatRates->where('rate', 23)->first()->id,
                'active' => true,
            ],
            [
                'reference' => 'ART-00003',
                'name' => 'Teclado Mecânico Logitech',
                'description' => 'Teclado mecânico com switches Cherry MX, iluminação RGB',
                'price' => 129.99,
                'vat_rate_id' => $vatRates->where('rate', 23)->first()->id,
                'active' => true,
            ],
            [
                'reference' => 'ART-00004',
                'name' => 'Cadeira de Escritório Ergonómica',
                'description' => 'Cadeira ergonómica com apoio lombar ajustável',
                'price' => 249.99,
                'vat_rate_id' => $vatRates->where('rate', 23)->first()->id,
                'active' => true,
            ],
            [
                'reference' => 'ART-00005',
                'name' => 'Impressora HP LaserJet',
                'description' => 'Impressora laser monocromática com scanner integrado',
                'price' => 199.99,
                'vat_rate_id' => $vatRates->where('rate', 23)->first()->id,
                'active' => true,
            ],
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }

        // Create 15 additional random articles using factory
        Article::factory()->count(15)->create([
            'vat_rate_id' => $vatRates->random()->id,
        ]);

        $this->command->info('Articles seeded successfully!');
    }
}
