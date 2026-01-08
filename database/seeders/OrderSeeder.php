<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Entity;
use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if orders already exist
        if (Order::count() > 0) {
            $this->command->info('Orders already exist. Skipping seeding.');
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

        $this->command->info('Creating sample orders...');

        // Create 5 draft orders
        for ($i = 0; $i < 5; $i++) {
            $order = Order::factory()->draft()->create([
                'entity_id' => $clients->random()->id,
            ]);

            // Add 2-5 lines to each order
            $lineCount = rand(2, 5);
            for ($j = 0; $j < $lineCount; $j++) {
                $article = $articles->random();
                $vatRate = $article->vatRate;
                OrderLine::factory()->create([
                    'order_id' => $order->id,
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

            $order->refresh();
            $order->calculateTotals();
        }

        // Create 3 closed orders
        for ($i = 0; $i < 3; $i++) {
            $order = Order::factory()->closed()->create([
                'entity_id' => $clients->random()->id,
            ]);

            // Add 2-5 lines to each order
            $lineCount = rand(2, 5);
            for ($j = 0; $j < $lineCount; $j++) {
                $article = $articles->random();
                $vatRate = $article->vatRate;
                OrderLine::factory()->create([
                    'order_id' => $order->id,
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

            $order->refresh();
            $order->calculateTotals();
        }

        $this->command->info('Orders seeded successfully!');
    }
}
