# 📹 Guia Completo de Demonstração - InovGest Multi-Tenant

Bem-vindo ao guia completo para apresentação das funcionalidades multi-tenant do InovGest!

## 📚 Documentos Disponíveis

Este repositório contém 4 documentos principais para ajudá-lo a preparar e gravar um vídeo profissional:

### 1. 📖 GUIA_APRESENTACAO_VIDEO.md
**O guia principal passo a passo**
- Preparação antes de gravar
- Script detalhado para cada parte do vídeo
- Explicações do que dizer e mostrar
- Dicas de gravação e edição
- Resolução de problemas

**👉 Use este como referência principal durante a gravação**

---

### 2. ✅ CHECKLIST_DEMO.md
**Checklist rápido de verificação**
- Lista de tarefas pré-gravação
- Checklist de conteúdo a cobrir
- Pontos-chave a destacar
- Problemas comuns e soluções
- Scripts prontos para copy-paste

**👉 Use este antes e durante a gravação**

---

### 3. 🎨 FLUXO_VISUAL_DEMO.md
**Storyboard e guia visual**
- Fluxo visual de cada parte
- Layout frame-by-frame
- Elementos visuais a adicionar
- Paleta de cores
- Sugestões de música e edição

**👉 Use este durante a edição do vídeo**

---

### 4. 🛠️ preparar_demo.sh
**Script automático de preparação**
- Limpa cache e sessões
- Reseta base de dados
- Cria planos de exemplo
- Verifica ambiente

**👉 Execute este antes de começar**

---

## 🚀 Quick Start

### Passo 1: Preparar Ambiente
```bash
# Executar script de preparação
./preparar_demo.sh

# Iniciar servidor
php artisan serve
```

### Passo 2: Verificar Documentação
```bash
# Ler guia principal
cat GUIA_APRESENTACAO_VIDEO.md

# Imprimir checklist
cat CHECKLIST_DEMO.md
```

### Passo 3: Gravar!
1. Abrir browser em `http://localhost:8000`
2. Seguir GUIA_APRESENTACAO_VIDEO.md
3. Verificar CHECKLIST_DEMO.md entre gravações

### Passo 4: Editar
1. Seguir sugestões em FLUXO_VISUAL_DEMO.md
2. Adicionar overlays, zoom, música
3. Adicionar timestamps na descrição

---

## 📊 Visão Geral da Demonstração

### Duração Total: 15-20 minutos

```
┌─────────────────────────────────────────────────┐
│  PARTE 1: Introdução (1-2 min)                  │
│  • Apresentação                                 │
│  • Agenda                                       │
├─────────────────────────────────────────────────┤
│  PARTE 2: Criação do Primeiro Tenant (3-4 min) │
│  • Registo de utilizador                       │
│  • Criar tenant                                │
│  • Redirecionamento para onboarding           │
├─────────────────────────────────────────────────┤
│  PARTE 3: Onboarding Wizard (4-5 min)          │
│  • Step 1: Branding                            │
│  • Step 2: Utilizadores                        │
│  • Step 3: Permissões                          │
│  • Conclusão                                   │
├─────────────────────────────────────────────────┤
│  PARTE 4: Gestão de Subscrições (5-6 min)     │
│  • Ver planos disponíveis                      │
│  • Subscrever com trial                        │
│  • Fazer upgrade                               │
│  • Fazer downgrade                             │
│  • Cancelar subscrição                         │
│  • Ver créditos                                │
│  • Histórico e audit logs                      │
│  • Dashboard de utilização                     │
├─────────────────────────────────────────────────┤
│  PARTE 5: Multi-Tenant (3-4 min)               │
│  • Criar segundo tenant                        │
│  • Navegar entre tenants                       │
│  • Mostrar isolamento de dados                 │
│  • Convidar utilizador existente               │
├─────────────────────────────────────────────────┤
│  PARTE 6: Conclusão (1 min)                    │
│  • Recap de funcionalidades                    │
│  • Despedida                                   │
└─────────────────────────────────────────────────┘
```

---

## ✨ Funcionalidades Demonstradas

### 🏢 Multi-Tenancy
- ✅ Criação de múltiplos tenants por utilizador
- ✅ Navegação fácil entre tenants
- ✅ Isolamento total de dados
- ✅ Configurações independentes por tenant
- ✅ Diferentes permissões por tenant

### 🎯 Onboarding
- ✅ Wizard guiado em 3 passos
- ✅ Branding personalizado (logo, cores)
- ✅ Convite de utilizadores
- ✅ Configuração de permissões
- ✅ Checklist de tarefas opcionais

### 💳 Subscrições
- ✅ Múltiplos planos com features e limites
- ✅ Free trial de 14 dias
- ✅ Upgrade imediato com pró-rata
- ✅ Downgrade agendado para próximo período
- ✅ Cancelamento com créditos proporcionais
- ✅ Sistema de créditos FIFO com expiração

### 📊 Gestão e Auditoria
- ✅ Dashboard de utilização em tempo real
- ✅ Gráficos de tendências
- ✅ Histórico completo com audit logs
- ✅ Rastreamento de todos os eventos
- ✅ Gestão de limites de uso

---

## 🎯 Dados de Exemplo

Use estes dados durante a demonstração para consistência:

```
👤 UTILIZADOR PRINCIPAL
   Nome: João Santos
   Email: joao.santos@example.com
   Password: (escolher uma)

🏢 TENANT 1
   Nome: InovSolutions Lda
   Slug: inovsolutions-lda
   Cores: #3B82F6 (azul), #10B981 (verde)

🏢 TENANT 2
   Nome: TechStart Unipessoal
   Slug: techstart-unipessoal
   Cores: #8B5CF6 (roxo), #F59E0B (laranja)

👥 UTILIZADORES PARA CONVIDAR
   1. Maria Silva (maria.silva@example.com) - Admin
   2. João Costa (joao.costa@example.com) - Member

📦 PLANOS DISPONÍVEIS
   • Free: €0/mês (1 user, 10 faturas/mês)
   • Pro: €29/mês (10 users, 500 faturas/mês)
   • Enterprise: €99/mês (ilimitado)
```

---

## 🛠️ Arquitetura Técnica

### Backend
```
Models:
├── Tenant.php
├── Subscription.php
├── Plan.php
├── OnboardingChecklist.php
├── TenantCredit.php
├── SubscriptionAuditLog.php
├── SubscriptionUsage.php
└── SubscriptionUsageHistory.php

Services:
├── SubscriptionService.php
├── TenantOnboardingService.php
├── CreditService.php
└── SubscriptionAuditService.php

Controllers:
├── TenantController.php
├── SubscriptionController.php
└── TenantOnboardingController.php
```

### Frontend (Vue.js + Inertia)
```
Pages:
├── Tenants/
│   └── Create.vue
├── Subscriptions/
│   ├── Plans.vue
│   ├── Index.vue
│   ├── History.vue
│   └── Dashboard.vue
└── Onboarding/
    ├── Start.vue
    ├── Step1Branding.vue
    ├── Step2Users.vue
    ├── Step3Permissions.vue
    └── Complete.vue
```

---

## 📋 Checklist Final

### Antes de Gravar
- [ ] Ambiente preparado (`./preparar_demo.sh`)
- [ ] Servidor a correr
- [ ] Browser limpo
- [ ] Planos verificados
- [ ] Dados de exemplo preparados
- [ ] Microfone testado
- [ ] Software de gravação pronto

### Durante Gravação
- [ ] Falar devagar e claramente
- [ ] Destacar elementos importantes
- [ ] Pausar após ações
- [ ] Mostrar validações e erros
- [ ] Manter energia

### Após Gravação
- [ ] Editar e cortar partes lentas
- [ ] Adicionar zoom e overlays
- [ ] Adicionar música de fundo
- [ ] Criar timestamps
- [ ] Adicionar legendas (opcional)

---

## 💡 Dicas Importantes

### ✅ FAZER
- Explicar o "porquê", não apenas o "o quê"
- Mostrar casos de uso reais
- Destacar isolamento de dados
- Enfatizar benefícios de segurança
- Demonstrar flexibilidade

### ❌ NÃO FAZER
- Apressar explicações
- Saltar validações importantes
- Ignorar mensagens de erro
- Assumir conhecimento técnico
- Esquecer de destacar features únicas

---

## 🆘 Resolução de Problemas

### Problemas Comuns

**Tenant não cria**
```bash
php artisan migrate:status
php artisan cache:clear
```

**Planos não aparecem**
```bash
php artisan db:seed --class=PlanSeeder
```

**Upload de logo falha**
```bash
php artisan storage:link
chmod -R 775 storage/
```

**Subscrição não cria**
```bash
# Verificar planos na BD
php artisan tinker --execute="App\Models\Plan::all(['name', 'slug'])"
```

---

## 📞 Suporte

Se encontrar problemas técnicos durante a preparação:

1. Verificar logs: `tail -f storage/logs/laravel.log`
2. Verificar console do browser (F12)
3. Executar `./preparar_demo.sh` novamente
4. Consultar CHECKLIST_DEMO.md → Secção "Problemas Comuns"

---

## 🎓 Recursos Adicionais

### Arquivos Relacionados
- `routes/web.php` - Todas as rotas
- `database/seeders/PlanSeeder.php` - Planos de exemplo
- `app/Models/` - Todos os models
- `app/Services/` - Lógica de negócio

### Comandos Úteis
```bash
# Ver status de migrações
php artisan migrate:status

# Listar rotas
php artisan route:list | grep tenant

# Verificar planos
php artisan tinker --execute="App\Models\Plan::count()"

# Ver tenants
php artisan tinker --execute="App\Models\Tenant::all(['name'])"
```

---

## 🎬 Ordem Recomendada de Leitura

Para melhor preparação, leia os documentos nesta ordem:

1. **README_DEMO.md** (este ficheiro) - Visão geral
2. **CHECKLIST_DEMO.md** - Preparação rápida
3. **GUIA_APRESENTACAO_VIDEO.md** - Guia detalhado
4. **FLUXO_VISUAL_DEMO.md** - Referência visual

Durante a gravação:
- Tenha **GUIA_APRESENTACAO_VIDEO.md** aberto
- Use **CHECKLIST_DEMO.md** para verificar progresso

Durante a edição:
- Siga **FLUXO_VISUAL_DEMO.md**

---

## 🚀 Começar Agora

```bash
# 1. Preparar ambiente
./preparar_demo.sh

# 2. Iniciar servidor
php artisan serve

# 3. Abrir browser
# Ir para http://localhost:8000

# 4. Começar gravação!
# Seguir GUIA_APRESENTACAO_VIDEO.md
```

---

## 📊 Estrutura de Timestamps Sugerida

Para adicionar na descrição do vídeo:

```
00:00 - Introdução e Apresentação
01:30 - Criação do Primeiro Tenant
04:00 - Wizard de Onboarding
04:30 - Step 1: Branding Personalizado
06:00 - Step 2: Convidar Utilizadores
07:00 - Step 3: Configurar Permissões
08:30 - Gestão de Subscrições
09:00 - Subscrever Plano com Free Trial
10:30 - Fazer Upgrade (Pró-rata)
12:00 - Fazer Downgrade (Agendado)
13:00 - Sistema de Créditos
14:00 - Histórico e Audit Logs
15:00 - Multi-Tenant Navigation
15:30 - Criar Segundo Tenant
16:30 - Navegar Entre Tenants
17:30 - Isolamento de Dados
18:30 - Conclusão e Recap
```

---

## ✅ Resultado Final

Após seguir este guia, terá um vídeo profissional de 15-20 minutos demonstrando:

✨ Sistema multi-tenant completo e funcional
✨ Onboarding intuitivo e guiado
✨ Gestão avançada de subscrições
✨ Billing rules e sistema de créditos
✨ Isolamento de dados e segurança
✨ Audit logging e rastreabilidade

**Boa sorte com a gravação! 🎬🚀**

---

## 📄 Licença

Este guia faz parte do projeto InovGest.

---

**Última atualização**: Janeiro 2026
**Versão**: 1.0
**Autor**: Equipa InovGest
