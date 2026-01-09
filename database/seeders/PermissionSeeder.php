<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = [
            // Entities (Clientes/Fornecedores)
            'entities.view',
            'entities.create',
            'entities.edit',
            'entities.delete',

            // Contacts
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.delete',

            // Proposals
            'proposals.view',
            'proposals.create',
            'proposals.edit',
            'proposals.delete',
            'proposals.close',
            'proposals.convert',

            // Orders (Encomendas)
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.close',
            'orders.convert',

            // Supplier Orders (Encomendas a Fornecedores)
            'supplier-orders.view',
            'supplier-orders.create',
            'supplier-orders.edit',
            'supplier-orders.delete',
            'supplier-orders.send',

            // Work Orders (Ordens de Trabalho)
            'work-orders.view',
            'work-orders.create',
            'work-orders.edit',
            'work-orders.delete',

            // Bank Accounts (Contas Bancárias)
            'bank-accounts.view',
            'bank-accounts.create',
            'bank-accounts.edit',
            'bank-accounts.delete',

            // Client Accounts (Conta Corrente Clientes)
            'client-accounts.view',
            'client-accounts.create',
            'client-accounts.edit',
            'client-accounts.delete',

            // Supplier Invoices (Faturas Fornecedores)
            'supplier-invoices.view',
            'supplier-invoices.create',
            'supplier-invoices.edit',
            'supplier-invoices.delete',
            'supplier-invoices.pay',

            // Calendar
            'calendar.view',
            'calendar.create',
            'calendar.edit',
            'calendar.delete',

            // Documents
            'documents.view',
            'documents.upload',
            'documents.download',
            'documents.delete',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Activity Log
            'activity-log.view',

            // Settings
            'settings.view',
            'settings.manage',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create default roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $managerRole = Role::firstOrCreate(['name' => 'Gestor']);
        $userRole = Role::firstOrCreate(['name' => 'Utilizador']);

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign limited permissions to manager
        $managerPermissions = Permission::whereNotIn('name', [
            'users.delete',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'settings.manage',
        ])->get();
        $managerRole->syncPermissions($managerPermissions);

        // Assign basic permissions to user
        $userPermissions = Permission::where('name', 'like', '%.view')
            ->orWhere('name', 'like', 'documents.%')
            ->get();
        $userRole->syncPermissions($userPermissions);

        $this->command->info('Permissions and roles created successfully!');
    }
}
