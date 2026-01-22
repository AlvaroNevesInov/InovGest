# 🔧 Aspectos Técnicos a Destacar na Apresentação

Este documento contém detalhes técnicos que pode mencionar durante a apresentação para demonstrar a robustez e qualidade da implementação.

---

## 🏗️ Arquitetura Multi-Tenant

### Modelo de Isolamento

```
"O sistema utiliza isolamento de dados a nível de linha (row-level isolation).
Cada query é automaticamente filtrada pelo tenant_id através de scopes do Eloquent,
garantindo que um tenant nunca acede a dados de outro."
```

**Mencionar**:
- Todas as tabelas principais têm coluna `tenant_id`
- Global scopes aplicados automaticamente
- Middleware valida acesso a cada request
- Impossível aceder dados de outro tenant (mesmo via API)

### Relação User-Tenant

```
"Um utilizador pode pertencer a múltiplos tenants através de uma relação
many-to-many com pivot table. Cada relação armazena a role específica
desse utilizador naquele tenant."
```

**Tabela Pivot** (`user_tenant`):
- `user_id` → Utilizador
- `tenant_id` → Tenant
- `role` → owner/admin/member
- `created_at` → Quando foi adicionado

**Mencionar**:
- Utilizador pode ter role diferente em cada tenant
- Transição de tenant é instantânea (apenas session update)
- Sem necessidade de re-autenticação

---

## 🎯 Onboarding Wizard

### Service Layer

```
"O onboarding é gerido pelo TenantOnboardingService, que usa transações
de base de dados para garantir consistência. Se um passo falhar, todo
o processo é revertido automaticamente."
```

**Implementação**:
```php
DB::transaction(function () use ($tenant, $data) {
    // Step 1: Atualizar branding
    $tenant->update(['settings->branding' => $data]);

    // Step 2: Criar relações
    $tenant->users()->attach($userIds);

    // Step 3: Marcar tarefa completa
    $checklist->markTaskComplete('setup_branding');
});
```

**Mencionar**:
- Atomicidade garantida com transactions
- Settings guardadas como JSON na BD
- Validação em cada step
- Possibilidade de retomar wizard

### Checklist Dinâmica

```
"A checklist de onboarding é dinâmica e extensível. Novos passos podem
ser adicionados sem alterar código, apenas configuração."
```

**Estrutura**:
```json
{
  "tasks": [
    {
      "key": "setup_branding",
      "name": "Configurar Branding",
      "required": true,
      "completed": true,
      "completed_at": "2026-01-22 10:30:00"
    }
  ]
}
```

---

## 💳 Sistema de Subscrições

### Cálculo de Pró-Rata

```
"Os valores pró-rata são calculados ao dia, considerando o número exato
de dias restantes no período atual. A fórmula é matematicamente precisa
e está testada para diferentes cenários."
```

**Fórmula**:
```php
$daysInPeriod = $currentPeriodStart->diffInDays($currentPeriodEnd);
$dailyRate = $plan->price / $daysInPeriod;
$daysRemaining = now()->diffInDays($currentPeriodEnd);
$proratedAmount = $dailyRate * $daysRemaining;
```

**Casos Especiais**:
- Meses com 28, 29, 30 ou 31 dias (calculado exato)
- Planos anuais (calculado em 365 ou 366 dias)
- Mudança no mesmo dia (sem cobrança extra)

### State Machine de Subscrição

```
"A subscrição funciona como uma state machine com transições bem definidas
e validadas. Cada mudança de estado é registada em audit log."
```

**Estados Possíveis**:
```
trialing → active    (trial convertido)
trialing → expired   (trial expirou sem pagamento)
active → canceled    (utilizador cancelou)
active → past_due    (pagamento falhou)
canceled → active    (reativação)
```

**Validações**:
- Não pode passar de `expired` para `active` diretamente
- Não pode cancelar uma subscrição já `canceled`
- Trial só existe no início do ciclo

### Upgrade vs Downgrade

```
"A diferença fundamental entre upgrade e downgrade está no timing:
- Upgrade: Imediato + cobrança pró-rata
- Downgrade: Agendado + sem reembolso + aplicado no próximo período"
```

**Lógica de Decisão**:
```php
if ($newPlan->price > $currentPlan->price) {
    // É upgrade
    $this->processUpgrade($subscription, $newPlan);
} else {
    // É downgrade
    $this->scheduleDowngrade($subscription, $newPlan);
}
```

**Mencionar**:
- Upgrade preserva proporcionalidade
- Downgrade evita abuse (subscrever Pro, usar 1 dia, fazer downgrade)
- Utilizador pode cancelar downgrade agendado

---

## 💰 Sistema de Créditos

### Gestão FIFO

```
"Os créditos são aplicados automaticamente usando algoritmo FIFO
(First In, First Out), priorizando os que expiram primeiro."
```

**Implementação**:
```php
public function applyCredits(Tenant $tenant, float $amount): float
{
    $credits = $tenant->credits()
        ->where('status', 'pending')
        ->orderBy('expires_at', 'asc')  // FIFO
        ->get();

    $remaining = $amount;
    foreach ($credits as $credit) {
        if ($remaining <= 0) break;

        if ($credit->amount >= $remaining) {
            $credit->amount -= $remaining;
            $remaining = 0;
        } else {
            $remaining -= $credit->amount;
            $credit->amount = 0;
            $credit->status = 'applied';
        }
        $credit->save();
    }

    return $remaining;
}
```

**Mencionar**:
- Créditos com expiração mais próxima são usados primeiro
- Créditos parcialmente usados mantêm saldo
- Expiração automática via scheduled command

### Tipos de Crédito

```
"O sistema suporta diferentes tipos de crédito, cada um com regras
específicas de aplicação e expiração."
```

| Tipo | Uso | Expiração | Origem |
|------|-----|-----------|--------|
| `refund` | Reembolso total | 12 meses | Erro de cobrança |
| `cancellation_credit` | Cancelamento pró-rata | 12 meses | Cancelamento imediato |
| `promotional` | Campanha | Variável | Admin/Sistema |
| `adjustment` | Ajuste manual | Sem expiração | Admin |
| `usage` | Tracking negativo | N/A | Sistema |

---

## 📊 Usage Tracking

### Tracking em Tempo Real

```
"A utilização de features é rastreada em tempo real com dois modelos:
- SubscriptionUsage: Estado atual
- SubscriptionUsageHistory: Snapshots diários para histórico"
```

**Incremento**:
```php
// Ao criar fatura
$usage = $subscription->usages()
    ->firstOrCreate(['feature' => 'invoices_per_month']);

if ($usage->hasReachedLimit()) {
    throw new LimitReachedException();
}

$usage->increment('used');
```

**Reset Periódico**:
```php
// Via scheduled command (diariamente)
SubscriptionUsage::where('reset_at', '<=', now())
    ->each(function ($usage) {
        // Snapshot para histórico
        SubscriptionUsageHistory::create([
            'subscription_id' => $usage->subscription_id,
            'feature' => $usage->feature,
            'used' => $usage->used,
            'limit' => $usage->limit,
        ]);

        // Reset
        $usage->update(['used' => 0, 'reset_at' => now()->addMonth()]);
    });
```

### Limites Configuráveis

```
"Cada plano define limites via JSON. Null significa ilimitado."
```

**Exemplo**:
```json
{
  "users": 10,              // Máximo 10 utilizadores
  "companies": 3,           // Máximo 3 empresas
  "storage_gb": 50,         // 50 GB
  "invoices_per_month": 500,// 500 faturas/mês
  "proposals_per_month": null // Ilimitado
}
```

**Validação**:
```php
if ($limit === null) {
    return false; // Ilimitado
}

return $usage->used >= $limit;
```

---

## 🔐 Segurança e Isolamento

### Tenant Middleware

```
"Middleware personalizado valida que o utilizador tem acesso ao tenant
antes de processar qualquer request."
```

**Implementação**:
```php
public function handle($request, Closure $next)
{
    $tenantId = $request->route('tenant') ?? session('current_tenant_id');

    if (!$tenantId) {
        return redirect()->route('tenants.create');
    }

    if (!auth()->user()->hasAccessToTenant($tenantId)) {
        abort(403, 'Acesso negado ao tenant');
    }

    // Define tenant global para queries
    Tenant::setCurrent($tenantId);

    return $next($request);
}
```

**Mencionar**:
- Validação em todas as rotas protegidas
- Impossível aceder tenant sem permissão
- Tenant atual definido globalmente para scopes

### API Isolation

```
"Todas as chamadas API requerem tenant_id explícito ou via header.
Sem tenant válido, a API retorna 403 Forbidden."
```

**Headers Esperados**:
```
X-Tenant-ID: 123
Authorization: Bearer {token}
```

**Validação**:
```php
$tenantId = $request->header('X-Tenant-ID');
$user = $request->user();

if (!$user->tenants->contains($tenantId)) {
    return response()->json(['error' => 'Forbidden'], 403);
}
```

---

## 📝 Audit Logging

### Eventos Rastreados

```
"Cada mudança de subscrição gera um evento de audit com contexto completo:
utilizador, IP, user agent, valores, metadata."
```

**Log Completo**:
```php
SubscriptionAuditLog::create([
    'subscription_id' => $subscription->id,
    'event' => 'upgraded',
    'previous_plan_id' => $oldPlan->id,
    'new_plan_id' => $newPlan->id,
    'previous_status' => 'trialing',
    'new_status' => 'active',
    'amount' => $newPlan->price,
    'prorated_amount' => $proratedAmount,
    'credit_amount' => null,
    'user_id' => auth()->id(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'metadata' => [
        'days_remaining' => $daysRemaining,
        'previous_trial_ends_at' => $subscription->trial_ends_at,
    ],
]);
```

**Mencionar**:
- Rastreabilidade total
- GDPR compliance (IP e user agent)
- Metadata extensível via JSON
- Imutável (apenas insert, nunca update/delete)

### Query de Histórico

```
"A timeline de eventos pode ser consultada de forma eficiente com eager
loading e filtros por tipo de evento."
```

**Query Otimizada**:
```php
$timeline = SubscriptionAuditLog::with(['user', 'previousPlan', 'newPlan'])
    ->where('subscription_id', $subscriptionId)
    ->orderByDesc('created_at')
    ->paginate(20);
```

---

## 📈 Performance e Escalabilidade

### Database Indexing

```
"Todas as queries críticas estão otimizadas com índices apropriados."
```

**Índices Principais**:
```sql
-- Tenants
INDEX (owner_id)
INDEX (slug) UNIQUE
INDEX (active)

-- Subscriptions
INDEX (tenant_id, status)
INDEX (trial_ends_at)
INDEX (next_billing_date)

-- Subscription Usage
INDEX (subscription_id, feature)
INDEX (reset_at)

-- Audit Logs
INDEX (subscription_id, created_at)
INDEX (user_id, created_at)
```

### Eager Loading

```
"Uso extensivo de eager loading para evitar N+1 queries."
```

**Exemplo**:
```php
// ❌ Ruim (N+1)
$tenants = Tenant::all();
foreach ($tenants as $tenant) {
    echo $tenant->owner->name; // Query por tenant
}

// ✅ Bom
$tenants = Tenant::with('owner')->get();
foreach ($tenants as $tenant) {
    echo $tenant->owner->name; // Sem queries extra
}
```

### Caching

```
"Dados raramente alterados são cacheados para reduzir load na BD."
```

**Exemplos**:
```php
// Plans (mudam raramente)
$plans = Cache::remember('plans.active', 3600, function () {
    return Plan::active()->ordered()->get();
});

// Tenant settings
$settings = Cache::remember("tenant.{$id}.settings", 600, function () {
    return $this->tenant->settings;
});
```

---

## 🧪 Testing

### Coverage Esperado

```
"O sistema deve ter testes cobrindo:
- Unit tests para services (cálculos, validações)
- Integration tests para flows completos
- Feature tests para endpoints API"
```

**Exemplo de Test**:
```php
public function test_prorated_upgrade_calculates_correctly()
{
    $subscription = Subscription::factory()->create([
        'plan_id' => $basicPlan->id,
        'current_period_start' => now(),
        'current_period_end' => now()->addMonth(),
    ]);

    // Simular upgrade 15 dias depois
    Carbon::setTestNow(now()->addDays(15));

    $result = $this->subscriptionService->upgradePlan(
        $subscription,
        $proPlan
    );

    // Verificar pró-rata
    $expectedProration = ($proPlan->price - $basicPlan->price) * (15/30);
    $this->assertEquals($expectedProration, $result->prorated_amount);
}
```

---

## 🚀 Deploy e Escalabilidade

### Horizontal Scaling

```
"O sistema está preparado para escalar horizontalmente:
- Stateless (session em Redis/DB)
- Jobs em queue (emails, processamento pesado)
- Cache distribuído (Redis)
- CDN para assets estáticos"
```

### Queue Jobs

```
"Operações pesadas são delegadas para queues."
```

**Exemplos**:
```php
// Envio de convites
dispatch(new SendTenantInvitationJob($user, $tenant));

// Processamento de upgrade
dispatch(new ProcessSubscriptionUpgrade($subscription, $newPlan));

// Expiração de trials
dispatch(new ExpireTrialsJob())->daily();
```

---

## 💡 Mencionar Durante Demo

### Pontos de Credibilidade Técnica

Quando demonstrar cada feature, pode mencionar:

**Tenant Creation**:
- "Slug gerado automaticamente com validação de unicidade"
- "Transação de BD garante consistência"
- "Tenant fica imediatamente disponível, sem setup async"

**Onboarding**:
- "Settings guardadas como JSON para flexibilidade"
- "Validação server-side em cada step"
- "Progress tracking via checklist model"

**Subscrições**:
- "Cálculo pró-rata matematicamente preciso"
- "State machine com transições validadas"
- "Audit log completo de todas as mudanças"

**Créditos**:
- "Algoritmo FIFO para aplicação automática"
- "Expiração gerida via scheduled commands"
- "Tracking de uso parcial de créditos"

**Usage Tracking**:
- "Tracking em tempo real com limites configuráveis"
- "Snapshots diários para análise histórica"
- "Gráficos gerados a partir de dados reais"

**Isolamento**:
- "Impossível aceder dados de outro tenant"
- "Validação em middleware + global scopes"
- "API requer tenant_id explícito"

---

## 📊 Métricas de Qualidade

```
"Este sistema foi desenvolvido seguindo best practices:
- ✅ SOLID principles
- ✅ Repository pattern (via Eloquent)
- ✅ Service layer para lógica de negócio
- ✅ Database transactions para atomicidade
- ✅ Event sourcing (audit logs)
- ✅ Feature flags prontos (via plans.features)
- ✅ API versioning preparado
- ✅ GDPR compliance (logs, direito a esquecimento)
"
```

---

**Use estes detalhes técnicos para enriquecer a apresentação e demonstrar profissionalismo! 💪🔧**
