<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Entity;
use App\Models\Article;
use App\Models\Order;
use App\Models\Proposal;
use App\Models\Contact;
use App\Models\Calendar;
use App\Models\BankAccount;
use App\Models\ClientAccount;
use App\Models\SupplierOrder;
use App\Models\SupplierInvoice;
use App\Models\WorkOrder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating default company...');

        // Get or create a default company
        $company = Company::first();

        if (!$company) {
            $company = Company::create([
                'name' => 'Empresa Principal',
                'nif' => '999999999',
                'address' => 'Rua Principal, 123',
                'postal_code' => '1000-001',
                'city' => 'Lisboa',
                'country_id' => \App\Models\Country::where('code', 'PT')->first()?->id,
                'phone' => '+351 210 000 000',
                'email' => 'geral@empresa.pt',
            ]);

            $this->command->info('Default company created: ' . $company->name);
        } else {
            $this->command->info('Using existing company: ' . $company->name);
        }

        // Attach all users to the company as owners
        $this->command->info('Attaching users to company...');

        $users = User::all();
        foreach ($users as $user) {
            if (!$user->companies()->where('company_id', $company->id)->exists()) {
                $user->companies()->attach($company->id, ['is_owner' => true]);
                $this->command->info("User {$user->email} attached to company as owner");
            }

            // Set current company if not set
            if (!$user->current_company_id) {
                $user->current_company_id = $company->id;
                $user->save();
                $this->command->info("Set current company for user {$user->email}");
            }
        }

        // Update all existing business records to belong to the default company
        $this->command->info('Updating existing records with company_id...');

        $models = [
            Entity::class => 'entities',
            Article::class => 'articles',
            Order::class => 'orders',
            Proposal::class => 'proposals',
            Contact::class => 'contacts',
            Calendar::class => 'calendars',
            BankAccount::class => 'bank_accounts',
            ClientAccount::class => 'client_accounts',
            SupplierOrder::class => 'supplier_orders',
            SupplierInvoice::class => 'supplier_invoices',
            WorkOrder::class => 'work_orders',
        ];

        foreach ($models as $modelClass => $tableName) {
            $updated = $modelClass::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)
                ->whereNull('company_id')
                ->update(['company_id' => $company->id]);

            if ($updated > 0) {
                $this->command->info("Updated {$updated} {$tableName} records");
            }
        }

        $this->command->info('Default tenant setup completed!');
    }
}
