<?php

namespace App\Services;

use App\Models\OnboardingChecklist;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantOnboardingService
{
    /**
     * Create a new tenant with automatic setup.
     */
    public function createTenant(User $owner, array $data): Tenant
    {
        return DB::transaction(function () use ($owner, $data) {
            // Create the tenant
            $tenant = Tenant::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'owner_id' => $owner->id,
                'active' => true,
                'settings' => $this->getDefaultSettings(),
            ]);

            // Attach the owner to the tenant with 'owner' role
            $tenant->users()->attach($owner->id, ['role' => 'owner']);

            // Set as current tenant for the user
            $owner->switchTenant($tenant->id);

            // Create default onboarding checklist
            OnboardingChecklist::createDefaultTasksForTenant($tenant->id);

            return $tenant;
        });
    }

    /**
     * Complete the onboarding wizard for a tenant.
     */
    public function completeOnboardingWizard(Tenant $tenant, array $wizardData): void
    {
        DB::transaction(function () use ($tenant, $wizardData) {
            // Step 1: Update branding settings
            if (isset($wizardData['branding'])) {
                $this->updateBranding($tenant, $wizardData['branding']);
                $this->markTaskCompleted($tenant->id, 'setup_branding');
            }

            // Step 2: Invite users
            if (isset($wizardData['users']) && !empty($wizardData['users'])) {
                $this->inviteUsers($tenant, $wizardData['users']);
                $this->markTaskCompleted($tenant->id, 'invite_users');
            }

            // Step 3: Setup permissions
            if (isset($wizardData['permissions'])) {
                $this->setupPermissions($tenant, $wizardData['permissions']);
                $this->markTaskCompleted($tenant->id, 'setup_permissions');
            }
        });
    }

    /**
     * Update tenant branding settings.
     */
    public function updateBranding(Tenant $tenant, array $branding): void
    {
        $settings = $tenant->settings ?? [];

        $settings['branding'] = [
            'logo_path' => $branding['logo_path'] ?? null,
            'primary_color' => $branding['primary_color'] ?? '#3b82f6',
            'secondary_color' => $branding['secondary_color'] ?? '#1e40af',
            'company_name' => $branding['company_name'] ?? $tenant->name,
        ];

        $tenant->update(['settings' => $settings]);
    }

    /**
     * Invite users to the tenant.
     */
    public function inviteUsers(Tenant $tenant, array $users): void
    {
        foreach ($users as $userData) {
            // Check if user exists
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                // Create new user with temporary password
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt(Str::random(16)), // Temporary password
                ]);

                // TODO: Send invitation email with password reset link
            }

            // Attach user to tenant with specified role
            $role = $userData['role'] ?? 'member';

            if (!$tenant->users()->where('user_id', $user->id)->exists()) {
                $tenant->users()->attach($user->id, ['role' => $role]);
            }
        }
    }

    /**
     * Setup permissions for the tenant.
     */
    public function setupPermissions(Tenant $tenant, array $permissions): void
    {
        $settings = $tenant->settings ?? [];

        $settings['permissions'] = [
            'default_member_permissions' => $permissions['member_permissions'] ?? [],
            'default_admin_permissions' => $permissions['admin_permissions'] ?? [],
            'custom_roles' => $permissions['custom_roles'] ?? [],
        ];

        $tenant->update(['settings' => $settings]);
    }

    /**
     * Mark a checklist task as completed.
     */
    public function markTaskCompleted(int $tenantId, string $taskKey): void
    {
        $task = OnboardingChecklist::where('tenant_id', $tenantId)
            ->where('task_key', $taskKey)
            ->first();

        if ($task) {
            $task->markAsCompleted();
        }
    }

    /**
     * Get default settings for a new tenant.
     */
    private function getDefaultSettings(): array
    {
        return [
            'branding' => [
                'logo_path' => null,
                'primary_color' => '#3b82f6',
                'secondary_color' => '#1e40af',
            ],
            'permissions' => [
                'default_member_permissions' => [
                    'view_entities',
                    'create_entities',
                    'view_contacts',
                    'create_contacts',
                    'view_calendar',
                ],
                'default_admin_permissions' => [
                    'view_entities',
                    'create_entities',
                    'edit_entities',
                    'delete_entities',
                    'view_contacts',
                    'create_contacts',
                    'edit_contacts',
                    'delete_contacts',
                    'view_calendar',
                    'manage_calendar',
                    'view_articles',
                    'manage_articles',
                    'manage_users',
                ],
            ],
            'features' => [
                'enable_calendar' => true,
                'enable_invoicing' => true,
                'enable_proposals' => true,
                'enable_work_orders' => true,
            ],
        ];
    }

    /**
     * Get onboarding progress for a tenant.
     */
    public function getOnboardingProgress(int $tenantId): array
    {
        $checklists = OnboardingChecklist::where('tenant_id', $tenantId)
            ->ordered()
            ->get();

        return [
            'tasks' => $checklists,
            'completion_percentage' => OnboardingChecklist::getCompletionPercentage($tenantId),
            'all_required_completed' => OnboardingChecklist::allRequiredTasksCompleted($tenantId),
            'total_tasks' => $checklists->count(),
            'completed_tasks' => $checklists->where('is_completed', true)->count(),
        ];
    }
}
