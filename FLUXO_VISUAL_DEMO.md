# 🎨 Fluxo Visual da Demonstração

## 📊 Visão Geral do Vídeo

```
┌─────────────────────────────────────────────────────────────────┐
│                    DEMONSTRAÇÃO MULTI-TENANT                     │
│                        InovGest (15-20 min)                      │
└─────────────────────────────────────────────────────────────────┘

1️⃣ INTRO (1-2 min)
    └─→ Apresentação + Agenda

2️⃣ PRIMEIRO TENANT (3-4 min)
    └─→ Registo → Criar Tenant → Dashboard

3️⃣ ONBOARDING (4-5 min)
    └─→ Step 1: Branding
    └─→ Step 2: Users
    └─→ Step 3: Permissions
    └─→ Complete

4️⃣ SUBSCRIÇÕES (5-6 min)
    └─→ Ver Planos → Trial → Upgrade → Downgrade → Cancelar
    └─→ Créditos → Histórico → Dashboard

5️⃣ MULTI-TENANT (3-4 min)
    └─→ Criar 2º Tenant → Navegar → Isolamento

6️⃣ CONCLUSÃO (1 min)
    └─→ Recap + Despedida
```

---

## 🎬 Storyboard Detalhado

### PARTE 1: INTRODUÇÃO (1-2 min)

```
┌──────────────────────────────────────────┐
│  FRAME 1: Tela de abertura               │
│  ┌────────────────────────────────────┐  │
│  │    🏢 InovGest Multi-Tenant        │  │
│  │    Sistema de Gestão SaaS          │  │
│  │                                     │  │
│  │    Apresentado por: [Nome]         │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Bem-vindos! Hoje vou mostrar..."    │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  FRAME 2: Agenda visual                  │
│  ┌────────────────────────────────────┐  │
│  │  Hoje vamos ver:                   │  │
│  │  ✓ Multi-Tenant Creation           │  │
│  │  ✓ Onboarding Wizard               │  │
│  │  ✓ Subscription Management         │  │
│  │  ✓ Tenant Navigation               │  │
│  │  ✓ Billing & Credits               │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
```

**Duração**: 1-2 minutos
**Transição**: Fade para browser

---

### PARTE 2: CRIAÇÃO DO PRIMEIRO TENANT (3-4 min)

```
┌──────────────────────────────────────────┐
│  FRAME 3: Página de Registo              │
│  ┌────────────────────────────────────┐  │
│  │  📝 Criar Conta                    │  │
│  │  ┌──────────────────────────────┐  │  │
│  │  │ Nome: João Santos            │  │  │
│  │  │ Email: joao.santos@...       │  │  │
│  │  │ Password: ********           │  │  │
│  │  │ [Registar]                   │  │  │
│  │  └──────────────────────────────┘  │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Primeiro, o utilizador regista-se   │
│     no sistema..."                       │
└──────────────────────────────────────────┘

            ↓ (após registo)

┌──────────────────────────────────────────┐
│  FRAME 4: Prompt para Criar Tenant       │
│  ┌────────────────────────────────────┐  │
│  │  ⚠️  Nenhum tenant encontrado      │  │
│  │                                     │  │
│  │  Para começar, crie o seu          │  │
│  │  primeiro tenant.                  │  │
│  │                                     │  │
│  │  [Criar Primeiro Tenant]           │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Sistema detecta que não tem tenant  │
│     e redireciona para criação..."       │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 5: Formulário de Tenant           │
│  ┌────────────────────────────────────┐  │
│  │  🏢 Criar Tenant                   │  │
│  │  ┌──────────────────────────────┐  │  │
│  │  │ Nome: InovSolutions Lda      │  │  │
│  │  │ Slug: inovsolutions-lda      │  │  │
│  │  │       (gerado auto)          │  │  │
│  │  │ [Criar]                      │  │  │
│  │  └──────────────────────────────┘  │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Utilizador define nome da empresa.  │
│     Slug gerado automaticamente..."      │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 6: Sucesso + Redirecionamento     │
│  ┌────────────────────────────────────┐  │
│  │  ✅ Tenant criado com sucesso!     │  │
│  │                                     │  │
│  │  Redirecionando para onboarding... │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
```

**Duração**: 3-4 minutos
**Transição**: Smooth scroll para onboarding

---

### PARTE 3: ONBOARDING WIZARD (4-5 min)

```
┌──────────────────────────────────────────┐
│  FRAME 7: Tela de Boas-vindas            │
│  ┌────────────────────────────────────┐  │
│  │  🎉 Bem-vindo ao InovGest!         │  │
│  │                                     │  │
│  │  [▓▓░░░░░░░░] 0% completo          │  │
│  │                                     │  │
│  │  Próximos passos:                  │  │
│  │  1️⃣ Branding                       │  │
│  │  2️⃣ Utilizadores                   │  │
│  │  3️⃣ Permissões                     │  │
│  │                                     │  │
│  │  [Começar] [Saltar]                │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Wizard guiado em 3 passos..."       │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 8: Step 1 - Branding              │
│  ┌────────────────────────────────────┐  │
│  │  🎨 Passo 1: Branding              │  │
│  │  [▓▓▓▓░░░░░░] 33%                  │  │
│  │                                     │  │
│  │  Empresa: [InovSolutions Lda]      │  │
│  │  Logo: [📎 Upload] logo.png        │  │
│  │  Cor 1: [🔵] #3B82F6               │  │
│  │  Cor 2: [🟢] #10B981               │  │
│  │                                     │  │
│  │  [← Voltar] [Próximo →]            │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Configurar identidade visual..."    │
│  💡 DESTACAR: Preview das cores          │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 9: Step 2 - Utilizadores          │
│  ┌────────────────────────────────────┐  │
│  │  👥 Passo 2: Convidar Equipa       │  │
│  │  [▓▓▓▓▓▓░░░░] 66%                  │  │
│  │                                     │  │
│  │  Utilizador 1:                     │  │
│  │  Nome: [Maria Silva]               │  │
│  │  Email: [maria.silva@...]          │  │
│  │  Role: [Admin ▼]                   │  │
│  │                                     │  │
│  │  [+ Adicionar outro]               │  │
│  │                                     │  │
│  │  [← Voltar] [Próximo →]            │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Convidar membros da equipa..."      │
│  💡 DESTACAR: Diferentes roles           │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 10: Step 3 - Permissões          │
│  ┌────────────────────────────────────┐  │
│  │  🔐 Passo 3: Permissões            │  │
│  │  [▓▓▓▓▓▓▓▓▓░] 90%                  │  │
│  │                                     │  │
│  │  Permissões para Members:          │  │
│  │  ☑ Ver Entidades                   │  │
│  │  ☑ Criar Contactos                 │  │
│  │  ☐ Eliminar Entidades              │  │
│  │  ☐ Gerir Faturação                 │  │
│  │                                     │  │
│  │  [← Voltar] [Concluir →]           │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Definir permissões granulares..."   │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 11: Conclusão                     │
│  ┌────────────────────────────────────┐  │
│  │  ✅ Configuração Completa!         │  │
│  │  [▓▓▓▓▓▓▓▓▓▓] 100%                 │  │
│  │                                     │  │
│  │  ✓ Branding configurado            │  │
│  │  ✓ Utilizadores convidados         │  │
│  │  ✓ Permissões definidas            │  │
│  │                                     │  │
│  │  [Ir para Dashboard]               │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
```

**Duração**: 4-5 minutos
**Transição**: Fade para dashboard de subscrições

---

### PARTE 4: GESTÃO DE SUBSCRIÇÕES (5-6 min)

```
┌──────────────────────────────────────────┐
│  FRAME 12: Planos Disponíveis            │
│  ┌────────────────────────────────────┐  │
│  │  💳 Escolha o Seu Plano            │  │
│  │                                     │  │
│  │  ┌──────┐ ┌──────┐ ┌──────┐       │  │
│  │  │ FREE │ │ PRO  │ │ENTER │       │  │
│  │  │ €0/m │ │€29/m │ │€99/m │       │  │
│  │  │      │ │ ⭐   │ │      │       │  │
│  │  │ 1 👤 │ │10 👤 │ │∞ 👤  │       │  │
│  │  └──────┘ └──────┘ └──────┘       │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Temos 3 planos principais..."       │
│  💡 DESTACAR: Features e limits          │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 13: Trial Ativo                   │
│  ┌────────────────────────────────────┐  │
│  │  📊 Minha Subscrição               │  │
│  │                                     │  │
│  │  Status: [Em Trial] 🔵             │  │
│  │  Plano: Pro                        │  │
│  │  Trial termina em: 14 dias         │  │
│  │                                     │  │
│  │  📈 Utilização:                    │  │
│  │  👥 Utilizadores:  2/10  [████░░]  │  │
│  │  📄 Faturas:       0/500 [░░░░░░]  │  │
│  │  💾 Storage:       0/50  [░░░░░░]  │  │
│  │                                     │  │
│  │  [Fazer Upgrade]                   │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Trial de 14 dias ativo..."          │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 14: Após Upgrade                  │
│  ┌────────────────────────────────────┐  │
│  │  ✅ Upgrade para Enterprise!       │  │
│  │                                     │  │
│  │  Status: [Ativa] 🟢                │  │
│  │  Plano: Enterprise (€99/mês)       │  │
│  │  Próxima cobrança: 01/02/2026      │  │
│  │                                     │  │
│  │  📈 Utilização:                    │  │
│  │  👥 Utilizadores:  2/∞   [░░░░░░]  │  │
│  │  📄 Faturas:       0/∞   [░░░░░░]  │  │
│  │  💾 Storage:       0/500 [░░░░░░]  │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Limites aumentaram, trial removido" │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 15: Downgrade Agendado            │
│  ┌────────────────────────────────────┐  │
│  │  ⚠️  Alteração Agendada             │  │
│  │                                     │  │
│  │  Downgrade para "Pro" agendado     │  │
│  │  para 01/02/2026                   │  │
│  │                                     │  │
│  │  [Cancelar Downgrade]              │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Downgrade é agendado, não imediato" │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 16: Créditos Disponíveis          │
│  ┌────────────────────────────────────┐  │
│  │  💰 Créditos da Conta              │  │
│  │                                     │  │
│  │  Total: €45.50                     │  │
│  │                                     │  │
│  │  ┌──────────────────────────────┐  │  │
│  │  │ Cancellation Credit          │  │  │
│  │  │ €45.50                       │  │  │
│  │  │ Expira: 22/01/2027           │  │  │
│  │  │ Status: Pending              │  │  │
│  │  └──────────────────────────────┘  │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Créditos de cancelamento válidos    │
│     por 12 meses..."                     │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 17: Histórico de Eventos          │
│  ┌────────────────────────────────────┐  │
│  │  📜 Histórico de Subscrições       │  │
│  │                                     │  │
│  │  ✓ 22/01 10:00 - Subscrição criada │  │
│  │    (Pro Trial)                     │  │
│  │  ✓ 22/01 10:15 - Upgrade           │  │
│  │    (Pro → Enterprise) €XX pró-rata │  │
│  │  ✓ 22/01 10:20 - Downgrade agendado│  │
│  │  ✓ 22/01 10:22 - Downgrade cancelado│  │
│  │  ✓ 22/01 10:25 - Cancelada         │  │
│  │    €45.50 em créditos              │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Audit log completo de todos         │
│     os eventos..."                       │
└──────────────────────────────────────────┘
```

**Duração**: 5-6 minutos
**Transição**: Slide para multi-tenant

---

### PARTE 5: MULTI-TENANT (3-4 min)

```
┌──────────────────────────────────────────┐
│  FRAME 18: Criar Segundo Tenant          │
│  ┌────────────────────────────────────┐  │
│  │  ➕ Novo Tenant                    │  │
│  │                                     │  │
│  │  Nome: TechStart Unipessoal        │  │
│  │  Slug: techstart-unipessoal        │  │
│  │                                     │  │
│  │  [Criar]                           │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Utilizador pode criar múltiplos     │
│     tenants..."                          │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 19: Menu de Navegação             │
│  ┌────────────────────────────────────┐  │
│  │  🏢 Tenant Ativo:                  │  │
│  │     TechStart Unipessoal ▼         │  │
│  │                                     │  │
│  │  ┌──────────────────────────────┐  │  │
│  │  │ ● InovSolutions Lda          │  │  │
│  │  │   TechStart Unipessoal       │  │  │
│  │  └──────────────────────────────┘  │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Dropdown para trocar de tenant..."  │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 20: Comparação Lado a Lado        │
│  ┌──────────────┬──────────────────────┐ │
│  │ InovSolutions│ TechStart            │ │
│  ├──────────────┼──────────────────────┤ │
│  │ 🎨 Azul/Verde│ 🎨 Roxo/Laranja      │ │
│  │ 👥 3 users   │ 👥 1 user            │ │
│  │ 📦 Enterprise│ 📦 Free              │ │
│  │ ✅ Ativa     │ ⏸️  Sem subscrição   │ │
│  └──────────────┴──────────────────────┘ │
│                                           │
│  🎤 "Isolamento total de dados..."       │
│  💡 DESTACAR: Dados completamente        │
│     diferentes                           │
└──────────────────────────────────────────┘
```

**Duração**: 3-4 minutos
**Transição**: Fade para conclusão

---

### PARTE 6: CONCLUSÃO (1 min)

```
┌──────────────────────────────────────────┐
│  FRAME 21: Recap Visual                  │
│  ┌────────────────────────────────────┐  │
│  │  ✅ Funcionalidades Demonstradas:  │  │
│  │                                     │  │
│  │  ✓ Multi-Tenant Creation           │  │
│  │  ✓ Onboarding Wizard (3 steps)     │  │
│  │  ✓ Subscription Management         │  │
│  │  ✓ Upgrade/Downgrade               │  │
│  │  ✓ Credits & Billing               │  │
│  │  ✓ Tenant Navigation               │  │
│  │  ✓ Data Isolation                  │  │
│  │  ✓ Audit Logging                   │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘

            ↓

┌──────────────────────────────────────────┐
│  FRAME 22: Tela de Encerramento          │
│  ┌────────────────────────────────────┐  │
│  │    🎉 Obrigado!                    │  │
│  │                                     │  │
│  │    Sistema Multi-Tenant            │  │
│  │    Pronto para Produção            │  │
│  │                                     │  │
│  │    github.com/...                  │  │
│  └────────────────────────────────────┘  │
│                                           │
│  🎤 "Obrigado por assistirem!"           │
└──────────────────────────────────────────┘
```

**Duração**: 1 minuto
**Fim**: Fade to black

---

## 🎯 Elementos Visuais a Adicionar na Edição

### Overlays de Texto:
```
Frame 8:  "Branding Personalizado por Tenant"
Frame 9:  "Convites por Email Automáticos"
Frame 10: "Permissões Granulares"
Frame 13: "14 Dias de Trial Grátis"
Frame 14: "Upgrade Imediato com Pró-rata"
Frame 15: "Downgrade Agendado (Não Imediato)"
Frame 16: "Créditos Válidos 12 Meses"
Frame 17: "Audit Trail Completo"
Frame 20: "Isolamento Total de Dados"
```

### Ícones/Badges:
```
✅ Concluído
⏳ Em progresso
🔵 Trial
🟢 Ativa
🟡 Agendada
🔴 Cancelada
⭐ Popular
💡 Dica
⚠️ Atenção
```

### Cores por Status:
```
Trial:     Azul (#3B82F6)
Ativa:     Verde (#10B981)
Agendada:  Amarelo (#F59E0B)
Cancelada: Vermelho (#EF4444)
Expirada:  Cinza (#6B7280)
```

---

## 📐 Layout Recomendado

### Resolução: 1920x1080 (Full HD)

```
┌───────────────────────────────────────────────────────┐
│  [Logo]               TITULO               [00:00]   │ ← Header
├───────────────────────────────────────────────────────┤
│                                                       │
│                                                       │
│               CONTEÚDO PRINCIPAL                      │ ← Main
│               (Browser, Slides, etc.)                 │
│                                                       │
│                                                       │
├───────────────────────────────────────────────────────┤
│  💡 Dica: Texto explicativo aqui...                  │ ← Footer
└───────────────────────────────────────────────────────┘
```

### Zonas de Destaque:
- **Superior Direito**: Timer/Progress
- **Superior Esquerdo**: Logo/Branding
- **Centro**: Conteúdo principal (80%)
- **Inferior**: Dicas, legendas, contexto (20%)

---

## 🎨 Paleta de Cores Sugerida

```
Background:    #FFFFFF (branco)
Text Primary:  #1F2937 (cinza escuro)
Text Secondary:#6B7280 (cinza médio)
Accent 1:      #3B82F6 (azul - InovSolutions)
Accent 2:      #10B981 (verde - InovSolutions)
Accent 3:      #8B5CF6 (roxo - TechStart)
Accent 4:      #F59E0B (laranja - TechStart)
Success:       #10B981 (verde)
Warning:       #F59E0B (amarelo)
Error:         #EF4444 (vermelho)
Info:          #3B82F6 (azul)
```

---

## 🎵 Sugestões de Música de Fundo

### Mood: Profissional, Moderno, Energético

**Segmentos**:
- Intro (0-2 min): Upbeat, energético
- Demo (2-17 min): Moderado, focado
- Conclusão (17-20 min): Uplifting, conclusivo

**Volume**:
- Durante narração: 20-30%
- Entre transições: 50-60%
- Intro/Outro: 70-80%

**Recomendações** (YouTube Audio Library):
- "The Elevator Bossa Nova" (Tom moderado)
- "Merry Go" (Energético)
- "Chill Wave" (Calmo e profissional)

---

## ✂️ Pontos de Corte Sugeridos

```
00:00-01:30 → INTRO (manter completo)
01:30-04:30 → TENANT (pode acelerar typing)
04:30-08:30 → ONBOARDING (acelerar formulários)
08:30-14:00 → SUBSCRIÇÕES (manter detalhado)
14:00-17:00 → MULTI-TENANT (pode acelerar)
17:00-19:00 → CONCLUSÃO (manter completo)
```

**Acelerar (1.5x)**:
- Digitação de formulários
- Loading screens
- Repetições similares

**Manter velocidade normal**:
- Explicações verbais
- Demonstrações de features únicas
- Comparações lado a lado

---

**Use este guia visual junto com GUIA_APRESENTACAO_VIDEO.md para uma apresentação perfeita! 🎬✨**
