<?php

namespace App\Http\Controllers;

use App\Mail\PlanChangedMail;
use App\Models\Plan;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display current subscription details
     */
    public function index()
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return redirect()->route('tenants.create');
        }

        $tenant = Tenant::with(['activeSubscription.plan', 'activeSubscription.scheduledPlan'])->find($tenantId);

        if (!$tenant) {
            return redirect()->route('tenants.create');
        }

        $subscription = $tenant->activeSubscription;
        $usageSummary = null;

        if ($subscription) {
            $usageSummary = $this->subscriptionService->getUsageSummary($subscription);
        }

        return Inertia::render('Subscriptions/Index', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'usageSummary' => $usageSummary,
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);

        // Check if user has access to this tenant
        if (!Auth::user()->hasAccessToTenant($tenant->id)) {
            return back()->withErrors(['error' => 'Você não tem acesso a este tenant.']);
        }

        // Check if already has an active subscription
        if ($tenant->hasActiveSubscription()) {
            return back()->withErrors(['error' => 'Este tenant já tem uma subscrição ativa.']);
        }

        try {
            DB::beginTransaction();

            $subscription = $this->subscriptionService->createSubscription($tenant, $plan);

            DB::commit();

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscrição criada com sucesso! Período de teste de ' . $plan->trial_days . ' dias iniciado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar subscrição: ' . $e->getMessage()]);
        }
    }

    /**
     * Upgrade to a higher plan
     */
    public function upgrade(Request $request, Plan $plan)
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return redirect()->route('tenants.create');
        }

        $tenant = Tenant::findOrFail($tenantId);

        if (!$tenant->hasActiveSubscription()) {
            return redirect()->route('plans.index')
                ->withErrors(['error' => 'Não tem uma subscrição ativa.']);
        }

        $subscription = $tenant->activeSubscription;

        // Verify it's an upgrade
        if (!$plan->isUpgradeFrom($subscription->plan)) {
            return back()->withErrors(['error' => 'Este plano não é um upgrade.']);
        }

        try {
            DB::beginTransaction();

            $oldPlan = $subscription->plan;
            $subscription = $this->subscriptionService->upgradePlan($subscription, $plan);

            // Send notification email
            /** @var \Illuminate\Contracts\Mail\Mailable $mail */
            $mail = new PlanChangedMail($subscription, $tenant, $oldPlan, $plan);
            Mail::to($tenant->owner->email)->send($mail);

            DB::commit();

            $message = 'Upgrade realizado com sucesso!';
            if ($subscription->prorated_amount > 0) {
                $message .= ' Valor pró-rata: €' . number_format($subscription->prorated_amount, 2, ',', '.');
            }

            return redirect()->route('subscriptions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao fazer upgrade: ' . $e->getMessage()]);
        }
    }

    /**
     * Downgrade to a lower plan
     */
    public function downgrade(Request $request, Plan $plan)
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return redirect()->route('tenants.create');
        }

        $tenant = Tenant::findOrFail($tenantId);

        if (!$tenant->hasActiveSubscription()) {
            return redirect()->route('plans.index')
                ->withErrors(['error' => 'Não tem uma subscrição ativa.']);
        }

        $subscription = $tenant->activeSubscription;

        // Verify it's a downgrade
        if (!$plan->isDowngradeFrom($subscription->plan)) {
            return back()->withErrors(['error' => 'Este plano não é um downgrade.']);
        }

        try {
            DB::beginTransaction();

            $oldPlan = $subscription->plan;
            $subscription = $this->subscriptionService->downgradePlan($subscription, $plan);

            // Send notification email
            /** @var \Illuminate\Contracts\Mail\Mailable $mail */
            $mail = new PlanChangedMail($subscription, $tenant, $oldPlan, $plan);
            Mail::to($tenant->owner->email)->send($mail);

            DB::commit();

            return redirect()->route('subscriptions.index')
                ->with('success', 'Downgrade agendado com sucesso! A mudança será efetuada em ' . $subscription->plan_change_scheduled_for->format('d/m/Y'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao agendar downgrade: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel scheduled plan change
     */
    public function cancelScheduledChange()
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return redirect()->route('tenants.create');
        }

        $tenant = Tenant::findOrFail($tenantId);

        if (!$tenant->hasActiveSubscription()) {
            return back()->withErrors(['error' => 'Não tem uma subscrição ativa.']);
        }

        $subscription = $tenant->activeSubscription;

        if (!$subscription->hasPlanChangeScheduled()) {
            return back()->withErrors(['error' => 'Não tem nenhuma alteração de plano agendada.']);
        }

        try {
            $this->subscriptionService->cancelScheduledPlanChange($subscription);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Alteração de plano cancelada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao cancelar alteração: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $validated = $request->validate([
            'immediately' => 'boolean',
        ]);

        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return redirect()->route('tenants.create');
        }

        $tenant = Tenant::findOrFail($tenantId);

        if (!$tenant->hasActiveSubscription()) {
            return back()->withErrors(['error' => 'Não tem uma subscrição ativa.']);
        }

        $subscription = $tenant->activeSubscription;

        try {
            $immediately = $validated['immediately'] ?? false;
            $this->subscriptionService->cancelSubscription($subscription, $immediately);

            $message = $immediately
                ? 'Subscrição cancelada imediatamente.'
                : 'Subscrição cancelada. Continuará ativa até ' . $subscription->ends_at->format('d/m/Y');

            return redirect()->route('subscriptions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao cancelar subscrição: ' . $e->getMessage()]);
        }
    }

    /**
     * Resume a canceled subscription
     */
    public function resume()
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return redirect()->route('tenants.create');
        }

        $tenant = Tenant::findOrFail($tenantId);
        $subscription = $tenant->subscription()->where('status', 'canceled')->first();

        if (!$subscription) {
            return back()->withErrors(['error' => 'Não tem uma subscrição cancelada para retomar.']);
        }

        try {
            $this->subscriptionService->resumeSubscription($subscription);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscrição retomada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao retomar subscrição: ' . $e->getMessage()]);
        }
    }
}
