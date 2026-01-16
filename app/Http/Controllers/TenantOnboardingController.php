<?php

namespace App\Http\Controllers;

use App\Models\OnboardingChecklist;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantOnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TenantOnboardingController extends Controller
{
    protected TenantOnboardingService $onboardingService;

    public function __construct(TenantOnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Show the onboarding wizard welcome page.
     */
    public function start()
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return redirect()->route('tenants.create');
        }

        $progress = $this->onboardingService->getOnboardingProgress($tenant->id);

        return Inertia::render('Onboarding/Start', [
            'tenant' => $tenant,
            'progress' => $progress,
        ]);
    }

    /**
     * Show step 1: Branding configuration.
     */
    public function step1()
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return redirect()->route('tenants.create');
        }

        return Inertia::render('Onboarding/Step1Branding', [
            'tenant' => $tenant,
            'currentSettings' => $tenant->settings['branding'] ?? [],
        ]);
    }

    /**
     * Save step 1: Branding configuration.
     */
    public function saveStep1(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('tenant-logos', 'public');
        }

        $brandingData = [
            'company_name' => $validated['company_name'] ?? $tenant->name,
            'logo_path' => $logoPath ?? ($tenant->settings['branding']['logo_path'] ?? null),
            'primary_color' => $validated['primary_color'] ?? '#3b82f6',
            'secondary_color' => $validated['secondary_color'] ?? '#1e40af',
        ];

        $this->onboardingService->updateBranding($tenant, $brandingData);
        $this->onboardingService->markTaskCompleted($tenant->id, 'setup_branding');

        return redirect()->route('onboarding.step2');
    }

    /**
     * Show step 2: Invite users.
     */
    public function step2()
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return redirect()->route('tenants.create');
        }

        $existingUsers = $tenant->users()->with('roles')->get();

        return Inertia::render('Onboarding/Step2Users', [
            'tenant' => $tenant,
            'existingUsers' => $existingUsers,
        ]);
    }

    /**
     * Save step 2: Invite users.
     */
    public function saveStep2(Request $request)
    {
        $validated = $request->validate([
            'users' => 'nullable|array',
            'users.*.name' => 'required_with:users|string|max:255',
            'users.*.email' => 'required_with:users|email|max:255',
            'users.*.role' => 'required_with:users|in:owner,admin,member',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        if (!empty($validated['users'])) {
            $this->onboardingService->inviteUsers($tenant, $validated['users']);
            $this->onboardingService->markTaskCompleted($tenant->id, 'invite_users');
        }

        return redirect()->route('onboarding.step3');
    }

    /**
     * Show step 3: Setup permissions.
     */
    public function step3()
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return redirect()->route('tenants.create');
        }

        $currentPermissions = $tenant->settings['permissions'] ?? [];

        return Inertia::render('Onboarding/Step3Permissions', [
            'tenant' => $tenant,
            'currentPermissions' => $currentPermissions,
            'availablePermissions' => $this->getAvailablePermissions(),
        ]);
    }

    /**
     * Save step 3: Setup permissions.
     */
    public function saveStep3(Request $request)
    {
        $validated = $request->validate([
            'member_permissions' => 'nullable|array',
            'admin_permissions' => 'nullable|array',
            'custom_roles' => 'nullable|array',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $this->onboardingService->setupPermissions($tenant, $validated);
        $this->onboardingService->markTaskCompleted($tenant->id, 'setup_permissions');

        return redirect()->route('onboarding.complete');
    }

    /**
     * Show onboarding completion page.
     */
    public function complete()
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return redirect()->route('tenants.create');
        }

        $progress = $this->onboardingService->getOnboardingProgress($tenant->id);

        return Inertia::render('Onboarding/Complete', [
            'tenant' => $tenant,
            'progress' => $progress,
        ]);
    }

    /**
     * Show the onboarding checklist.
     */
    public function checklist()
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        if (!$tenant) {
            return redirect()->route('tenants.create');
        }

        $progress = $this->onboardingService->getOnboardingProgress($tenant->id);

        return Inertia::render('Onboarding/Checklist', [
            'tenant' => $tenant,
            'progress' => $progress,
        ]);
    }

    /**
     * Toggle a checklist task completion status.
     */
    public function toggleTask(Request $request, $taskId)
    {
        $task = OnboardingChecklist::findOrFail($taskId);
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->currentTenant;

        // Verify the task belongs to the current tenant
        if ($task->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($task->is_completed) {
            $task->markAsIncomplete();
        } else {
            $task->markAsCompleted();
        }

        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'progress' => $this->onboardingService->getOnboardingProgress($tenant->id),
        ]);
    }

    /**
     * Skip the onboarding wizard.
     */
    public function skip()
    {
        return redirect()->route('dashboard');
    }

    /**
     * Get available permissions list.
     */
    private function getAvailablePermissions(): array
    {
        return [
            'entities' => [
                'view_entities' => 'Ver entidades',
                'create_entities' => 'Criar entidades',
                'edit_entities' => 'Editar entidades',
                'delete_entities' => 'Eliminar entidades',
            ],
            'contacts' => [
                'view_contacts' => 'Ver contactos',
                'create_contacts' => 'Criar contactos',
                'edit_contacts' => 'Editar contactos',
                'delete_contacts' => 'Eliminar contactos',
            ],
            'calendar' => [
                'view_calendar' => 'Ver calendário',
                'manage_calendar' => 'Gerir calendário',
            ],
            'articles' => [
                'view_articles' => 'Ver artigos',
                'manage_articles' => 'Gerir artigos',
            ],
            'financial' => [
                'view_invoices' => 'Ver faturas',
                'manage_invoices' => 'Gerir faturas',
                'view_proposals' => 'Ver propostas',
                'manage_proposals' => 'Gerir propostas',
            ],
            'users' => [
                'view_users' => 'Ver utilizadores',
                'manage_users' => 'Gerir utilizadores',
            ],
        ];
    }
}
