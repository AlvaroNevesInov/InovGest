<?php

namespace Database\Seeders;

use App\Models\Proposal;
use App\Models\ProposalLine;
use App\Models\Entity;
use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if proposals already exist
        if (Proposal::count() > 0) {
            $this->command->info('Proposals already exist. Skipping seeding.');
            return;
        }

        // Check if we have clients and articles
        $clients = Entity::clients()->get();
        $articles = Article::all();

        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Please run EntitySeeder first.');
            return;
        }

        if ($articles->isEmpty()) {
            $this->command->warn('No articles found. Please run ArticleSeeder first.');
            return;
        }

        $this->command->info('Creating sample proposals...');

        // Get the default company
        $company = \App\Models\Company::first();

        // Create 5 draft proposals
        for ($i = 0; $i < 5; $i++) {
            $proposal = Proposal::factory()->draft()->create([
                'company_id' => $company->id,
                'entity_id' => $clients->random()->id,
            ]);

            // Add 2-5 lines to each proposal
            $lineCount = rand(2, 5);
            for ($j = 0; $j < $lineCount; $j++) {
                $article = $articles->random();
                $vatRate = $article->vatRate;
                ProposalLine::factory()->create([
                    'proposal_id' => $proposal->id,
                    'article_id' => $article->id,
                    'article_reference' => $article->reference,
                    'article_name' => $article->name,
                    'description' => $article->description,
                    'unit_price' => $article->price,
                    'vat_rate_id' => $article->vat_rate_id,
                    'vat_rate' => $vatRate ? $vatRate->rate : 0,
                    'sort_order' => $j,
                ]);
            }

            $proposal->refresh();
            $proposal->calculateTotals();
        }

        // Create 3 closed proposals
        for ($i = 0; $i < 3; $i++) {
            $proposal = Proposal::factory()->closed()->create([
                'company_id' => $company->id,
                'entity_id' => $clients->random()->id,
            ]);

            // Add 2-5 lines to each proposal
            $lineCount = rand(2, 5);
            for ($j = 0; $j < $lineCount; $j++) {
                $article = $articles->random();
                $vatRate = $article->vatRate;
                ProposalLine::factory()->create([
                    'proposal_id' => $proposal->id,
                    'article_id' => $article->id,
                    'article_reference' => $article->reference,
                    'article_name' => $article->name,
                    'description' => $article->description,
                    'unit_price' => $article->price,
                    'vat_rate_id' => $article->vat_rate_id,
                    'vat_rate' => $vatRate ? $vatRate->rate : 0,
                    'sort_order' => $j,
                ]);
            }

            $proposal->refresh();
            $proposal->calculateTotals();
        }

        // Create 2 expired proposals
        for ($i = 0; $i < 2; $i++) {
            $proposal = Proposal::factory()->expired()->draft()->create([
                'company_id' => $company->id,
                'entity_id' => $clients->random()->id,
            ]);

            // Add 2-4 lines to each proposal
            $lineCount = rand(2, 4);
            for ($j = 0; $j < $lineCount; $j++) {
                $article = $articles->random();
                $vatRate = $article->vatRate;
                ProposalLine::factory()->create([
                    'proposal_id' => $proposal->id,
                    'article_id' => $article->id,
                    'article_reference' => $article->reference,
                    'article_name' => $article->name,
                    'description' => $article->description,
                    'unit_price' => $article->price,
                    'vat_rate_id' => $article->vat_rate_id,
                    'vat_rate' => $vatRate ? $vatRate->rate : 0,
                    'sort_order' => $j,
                ]);
            }

            $proposal->refresh();
            $proposal->calculateTotals();
        }

        $this->command->info('Proposals seeded successfully!');
    }
}
