<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Seed configuration tables
        $this->call([
            CountrySeeder::class,
            VatRateSeeder::class,
            ContactFunctionSeeder::class,
            ArticleSeeder::class,
        ]);

        // Create sample entities
        $this->command->info('Creating sample entities...');

        // Get Portugal country for realistic data
        $portugalId = \App\Models\Country::where('code', 'PT')->first()?->id;

        // Create 10 clients
        \App\Models\Entity::factory()
            ->count(10)
            ->client()
            ->create([
                'country_id' => $portugalId,
            ]);

        // Create 5 suppliers
        \App\Models\Entity::factory()
            ->count(5)
            ->supplier()
            ->create([
                'country_id' => $portugalId,
            ]);

        // Create 3 entities that are both client and supplier
        \App\Models\Entity::factory()
            ->count(3)
            ->create([
                'type' => 'both',
                'country_id' => $portugalId,
            ]);

        // Create 1 inactive entity
        \App\Models\Entity::factory()
            ->inactive()
            ->create([
                'country_id' => $portugalId,
            ]);

        // Create contacts for each entity
        $this->command->info('Creating sample contacts...');

        $entities = \App\Models\Entity::all();
        $contactFunctions = \App\Models\ContactFunction::all();

        foreach ($entities as $entity) {
            // Create 1-3 contacts per entity
            $contactCount = rand(1, 3);

            for ($i = 0; $i < $contactCount; $i++) {
                \App\Models\Contact::factory()->create([
                    'entity_id' => $entity->id,
                    'contact_function_id' => $contactFunctions->random()->id,
                ]);
            }
        }

        $this->command->info('Database seeding completed successfully!');
    }
}
