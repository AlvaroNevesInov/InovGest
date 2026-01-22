# 🎯 Guia de Acesso - Planos e Subscrições

## 📍 URLs de Acesso

### 1️⃣ **Ver Planos Disponíveis**
```
URL: /subscriptions/plans
Rota: subscriptions.plans
```

**O que mostra:**
- Lista de todos os planos disponíveis (Free, Pro, Enterprise, etc.)
- Comparação de features e limites
- Preços mensais/anuais
- Botões para subscrever/fazer upgrade/downgrade

**Como aceder:**
1. No browser: `http://localhost:8000/subscriptions/plans`
2. OU no dashboard: Procure menu "Subscrições" → "Planos"

---

### 2️⃣ **Ver Minha Subscrição Atual**
```
URL: /subscriptions
Rota: subscriptions.index
```

**O que mostra:**
- Plano atual
- Status (Trial, Ativa, Cancelada, Expirada)
- Data de próxima cobrança
- Utilização atual vs limites
- Opções para alterar plano ou cancelar

**Como aceder:**
1. No browser: `http://localhost:8000/subscriptions`
2. OU no dashboard: Menu "Subscrições" → "Minha Subscrição"

---

### 3️⃣ **Dashboard de Subscrições (Gráficos)**
```
URL: /subscriptions/dashboard
Rota: subscriptions.dashboard
```

**O que mostra:**
- Gráficos de utilização ao longo do tempo
- Tendências de uso de cada feature
- Resumo de créditos disponíveis
- Alertas de limites próximos

**Como aceder:**
1. No browser: `http://localhost:8000/subscriptions/dashboard`
2. OU no menu de subscrições

---

### 4️⃣ **Histórico de Subscrições**
```
URL: /subscriptions/history
Rota: subscriptions.history
```

**O que mostra:**
- Timeline de todos os eventos de subscrição
- Logs de mudanças de plano
- Créditos recebidos/utilizados
- Valores cobrados e pró-rata

**Como aceder:**
1. No browser: `http://localhost:8000/subscriptions/history`
2. OU no menu: "Subscrições" → "Histórico"

---

## 🔄 Ações Disponíveis

### **Subscrever a um Plano**
```
POST /subscriptions/subscribe/{plan_id}
```

Exemplo:
- Clica em "Começar Teste Grátis" num plano
- Sistema cria subscrição com trial de 14 dias

---

### **Fazer Upgrade**
```
POST /subscriptions/upgrade/{plan_id}
```

Exemplo:
- Está no plano "Pro"
- Clica "Fazer Upgrade" no plano "Enterprise"
- Cobrança pró-rata imediata
- Limites aumentam instantaneamente

---

### **Fazer Downgrade**
```
POST /subscriptions/downgrade/{plan_id}
```

Exemplo:
- Está no plano "Enterprise"
- Clica "Fazer Downgrade" no plano "Pro"
- Downgrade **agendado** para próximo período
- Pode cancelar antes de acontecer

---

### **Cancelar Subscrição**
```
POST /subscriptions/cancel
```

Opções:
1. **Cancelar no fim do período**: Mantém acesso até fim do que já pagou
2. **Cancelar imediatamente**: Recebe crédito pró-rata, perde acesso já

---

### **Reativar Subscrição**
```
POST /subscriptions/resume
```

- Só disponível se cancelou antes
- Créditos são aplicados automaticamente

---

### **Cancelar Downgrade Agendado**
```
POST /subscriptions/cancel-scheduled
```

- Remove downgrade que estava agendado
- Mantém plano atual

---

## 🎨 Adicionar Menu de Navegação

Se ainda não tem menu visível, adicione ao **Authenticated Layout** ou **Dashboard**:

### Opção 1: Dropdown no Menu Principal

Adicione ao `AuthenticatedLayout.vue`:

```vue
<!-- Dropdown for Subscrições -->
<Dropdown align="left" width="48">
    <template #trigger>
        <button class="inline-flex items-center px-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 h-16">
            Subscrições
            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </template>
    <template #content>
        <DropdownLink :href="route('subscriptions.plans')">Ver Planos</DropdownLink>
        <DropdownLink :href="route('subscriptions.index')">Minha Subscrição</DropdownLink>
        <DropdownLink :href="route('subscriptions.dashboard')">Dashboard</DropdownLink>
        <DropdownLink :href="route('subscriptions.history')">Histórico</DropdownLink>
    </template>
</Dropdown>
```

### Opção 2: Links Diretos no Dashboard

Adicione cards no Dashboard:

```vue
<!-- Card de Subscrição -->
<Link :href="route('subscriptions.plans')" class="block p-6 bg-white rounded-lg shadow hover:shadow-lg transition">
    <h3 class="text-lg font-semibold mb-2">Planos e Subscrições</h3>
    <p class="text-gray-600">Gerir o seu plano atual ou fazer upgrade</p>
</Link>
```

---

## 🧪 Testar Funcionalidades

### 1. **Criar Planos** (se ainda não existem)

Execute o seeder:
```bash
php artisan db:seed --class=PlanSeeder
```

Isto cria:
- Free (€0/mês)
- Pro (€29/mês)
- Pro Anual (€278/ano)
- Enterprise (€99/mês)
- Enterprise Anual (€950/ano)

---

### 2. **Fluxo Completo de Teste**

#### Passo 1: Ver Planos
```
http://localhost:8000/subscriptions/plans
```
✅ Deve ver cards com todos os planos

#### Passo 2: Subscrever ao Pro (Trial)
- Clique "Começar Teste Grátis" no plano Pro
- Deve criar subscrição com trial de 14 dias

#### Passo 3: Ver Subscrição
```
http://localhost:8000/subscriptions
```
✅ Deve ver:
- Status: "Em Trial"
- Plano: "Pro"
- Trial termina em: 14 dias
- Utilização: 0/500 faturas, 0/10 users, etc.

#### Passo 4: Fazer Upgrade para Enterprise
```
http://localhost:8000/subscriptions/plans
```
- Clique "Fazer Upgrade" no Enterprise
- Deve calcular pró-rata e atualizar imediatamente

#### Passo 5: Ver Dashboard
```
http://localhost:8000/subscriptions/dashboard
```
✅ Deve ver gráficos de utilização

#### Passo 6: Fazer Downgrade para Pro
- Volta aos planos
- Clique "Fazer Downgrade" no Pro
- Deve agendar para próxima data de cobrança

#### Passo 7: Ver Histórico
```
http://localhost:8000/subscriptions/history
```
✅ Deve ver timeline:
- Subscrição criada (Pro Trial)
- Upgrade para Enterprise
- Downgrade agendado para Pro

---

## 📸 Screenshots do Fluxo

### 1. Planos
![Planos](https://via.placeholder.com/800x400?text=Lista+de+Planos)
- Cards lado a lado
- Features e limites
- Preços destacados
- Badge "Popular"

### 2. Minha Subscrição
![Subscrição](https://via.placeholder.com/800x400?text=Minha+Subscricao)
- Status badge (Trial/Ativa/Cancelada)
- Progress bars de utilização
- Botões de ação

### 3. Dashboard
![Dashboard](https://via.placeholder.com/800x400?text=Dashboard+Subscricao)
- Gráficos de uso ao longo do tempo
- Cards de resumo
- Alertas

### 4. Histórico
![Histórico](https://via.placeholder.com/800x400?text=Historico)
- Timeline de eventos
- Detalhes de cada mudança
- Valores cobrados/creditados

---

## 🔑 Verificação Rápida

Execute estes comandos para verificar se está tudo OK:

```bash
# 1. Verificar se planos existem
php artisan tinker --execute="echo App\Models\Plan::count() . ' planos';"

# 2. Verificar rotas de subscrição
php artisan route:list | grep subscriptions

# 3. Ver planos detalhados
php artisan tinker --execute="App\Models\Plan::all(['name', 'price', 'billing_period'])->each(fn(\$p) => print(\$p->name . ': €' . \$p->price . '/' . \$p->billing_period . PHP_EOL));"
```

---

## 🎯 Quick Start

**Para testar AGORA:**

1. Abra: `http://localhost:8000/subscriptions/plans`
2. Se não houver planos: Execute `php artisan db:seed --class=PlanSeeder`
3. Clique "Começar Teste Grátis" num plano
4. Explore as outras páginas!

---

## 📚 Documentação Relacionada

- **Guia de demonstração em vídeo**: `GUIA_APRESENTACAO_VIDEO.md`
- **Checklist de demo**: `CHECKLIST_DEMO.md`
- **Aspectos técnicos**: `ASPECTOS_TECNICOS_DESTACAR.md`

---

**Boas explorações! 🚀**
