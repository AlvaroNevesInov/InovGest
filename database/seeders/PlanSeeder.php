<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Plano gratuito para começar. Perfeito para freelancers e pequenos negócios.',
                'price' => 0.00,
                'billing_period' => 'monthly',
                'trial_days' => 14,
                'features' => [
                    'calendar',
                    'contacts',
                    'articles',
                    'basic_invoicing',
                ],
                'limits' => [
                    'users' => 1,
                    'companies' => 1,
                    'storage_gb' => 1,
                    'invoices_per_month' => 10,
                    'proposals_per_month' => 5,
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Plano profissional com mais recursos. Ideal para equipas em crescimento.',
                'price' => 29.00,
                'billing_period' => 'monthly',
                'trial_days' => 14,
                'features' => [
                    'calendar',
                    'contacts',
                    'articles',
                    'invoicing',
                    'proposals',
                    'work_orders',
                    'advanced_reports',
                    'email_support',
                ],
                'limits' => [
                    'users' => 10,
                    'companies' => 3,
                    'storage_gb' => 50,
                    'invoices_per_month' => 500,
                    'proposals_per_month' => 100,
                ],
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro Anual',
                'slug' => 'pro-yearly',
                'description' => 'Plano profissional com faturação anual. Poupe 20%!',
                'price' => 278.00, // 29 * 12 * 0.8 = 278.40 (arredondado)
                'billing_period' => 'yearly',
                'trial_days' => 14,
                'features' => [
                    'calendar',
                    'contacts',
                    'articles',
                    'invoicing',
                    'proposals',
                    'work_orders',
                    'advanced_reports',
                    'email_support',
                ],
                'limits' => [
                    'users' => 10,
                    'companies' => 3,
                    'storage_gb' => 50,
                    'invoices_per_month' => 500,
                    'proposals_per_month' => 100,
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Plano empresarial sem limites. Para empresas que precisam de tudo.',
                'price' => 99.00,
                'billing_period' => 'monthly',
                'trial_days' => 14,
                'features' => [
                    'calendar',
                    'contacts',
                    'articles',
                    'invoicing',
                    'proposals',
                    'work_orders',
                    'advanced_reports',
                    'priority_support',
                    'custom_branding',
                    'api_access',
                    'advanced_permissions',
                ],
                'limits' => [
                    'users' => null, // Ilimitado
                    'companies' => null, // Ilimitado
                    'storage_gb' => 500,
                    'invoices_per_month' => null, // Ilimitado
                    'proposals_per_month' => null, // Ilimitado
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Enterprise Anual',
                'slug' => 'enterprise-yearly',
                'description' => 'Plano empresarial com faturação anual. Poupe 20%!',
                'price' => 950.00, // 99 * 12 * 0.8 = 950.40 (arredondado)
                'billing_period' => 'yearly',
                'trial_days' => 14,
                'features' => [
                    'calendar',
                    'contacts',
                    'articles',
                    'invoicing',
                    'proposals',
                    'work_orders',
                    'advanced_reports',
                    'priority_support',
                    'custom_branding',
                    'api_access',
                    'advanced_permissions',
                ],
                'limits' => [
                    'users' => null, // Ilimitado
                    'companies' => null, // Ilimitado
                    'storage_gb' => 500,
                    'invoices_per_month' => null, // Ilimitado
                    'proposals_per_month' => null, // Ilimitado
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
