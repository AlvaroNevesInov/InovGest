# 🎬 Guia de Apresentação em Vídeo - Multi-Tenancy InovGest

## 📋 Checklist de Preparação

### Antes de Gravar:
- [ ] Base de dados limpa (sem tenants de teste antigos)
- [ ] Verificar se há planos criados na BD (Free, Basic, Pro, Enterprise)
- [ ] Browser limpo (sem sessões antigas)
- [ ] Testar todo o fluxo uma vez antes de gravar
- [ ] Preparar dados de exemplo (nomes de empresas, utilizadores, etc.)

### Dados de Exemplo para Usar:
```
Tenant 1: "InovSolutions Lda"
Tenant 2: "TechStart Unipessoal"
Utilizadores para convidar:
  - maria.silva@example.com (Admin)
  - joao.costa@example.com (Member)
```

---

## 🎯 Estrutura do Vídeo (15-20 minutos)

### **PARTE 1: Introdução (1-2 min)**
### **PARTE 2: Criação do Primeiro Tenant (3-4 min)**
### **PARTE 3: Onboarding Wizard (4-5 min)**
### **PARTE 4: Gestão de Subscrições (5-6 min)**
### **PARTE 5: Multi-Tenant - Criação e Navegação (3-4 min)**
### **PARTE 6: Conclusão (1 min)**

---

## 📝 PARTE 1: INTRODUÇÃO (1-2 min)

### O que dizer:
```
"Bem-vindos! Hoje vou apresentar o sistema multi-tenant que desenvolvemos
para o InovGest. Este sistema permite que cada cliente tenha o seu próprio
espaço isolado, com gestão completa de subscrições, free trials, e
possibilidade de criar múltiplos tenants.

Vou mostrar:
1. Como um utilizador cria o seu primeiro tenant
2. O wizard de onboarding completo
3. Sistema de subscrições com planos, upgrades e downgrades
4. Gestão de múltiplos tenants e navegação entre eles
5. Sistema de créditos e billing rules

Vamos começar!"
```

### O que mostrar:
- Ecrã inicial (dashboard ou página de login)
- Slide rápido com os tópicos que vai cobrir

---

## 📝 PARTE 2: CRIAÇÃO DO PRIMEIRO TENANT (3-4 min)

### Passo 1: Registo de Novo Utilizador
**URL**: `/register`

**O que fazer**:
1. Preencher formulário de registo:
   - Nome: "João Santos"
   - Email: "joao.santos@example.com"
   - Password: (escolher uma)
2. Submeter formulário
3. Fazer login (se necessário)

**O que dizer**:
```
"Quando um utilizador se regista pela primeira vez no sistema,
ele ainda não tem nenhum tenant associado. O primeiro passo é
criar o seu primeiro tenant - que é a sua empresa ou organização."
```

---

### Passo 2: Dashboard Detecta Ausência de Tenant
**URL**: `/dashboard` (automático após login)

**O que mostrar**:
- Dashboard vazio ou prompt para criar tenant
- Botão "Criar Primeiro Tenant" ou similar

**O que dizer**:
```
"O sistema detecta automaticamente que o utilizador não tem nenhum
tenant e redireciona para a página de criação."
```

---

### Passo 3: Criar Primeiro Tenant
**URL**: `/tenants/create`

**O que fazer**:
1. Preencher nome: "InovSolutions Lda"
2. (Slug é gerado automaticamente: "inovsolutions-lda")
3. Clicar "Criar Tenant"

**O que dizer**:
```
"Aqui o utilizador define o nome da sua empresa. O sistema gera
automaticamente um slug único que será usado para identificação.
Este utilizador torna-se automaticamente o proprietário (owner)
deste tenant."
```

**O que acontece nos bastidores** (mencionar brevemente):
- Tenant criado na BD
- Utilizador associado como "owner"
- Tenant definido como ativo (current_tenant_id)
- Checklist de onboarding criada
- Redirecionamento para onboarding

---

## 📝 PARTE 3: ONBOARDING WIZARD (4-5 min)

### Passo 4: Ecrã de Boas-vindas
**URL**: `/onboarding`

**O que mostrar**:
- Mensagem de boas-vindas
- Barra de progresso (0%)
- Lista dos 3 passos: Branding, Utilizadores, Permissões
- Botões: "Começar Configuração" e "Saltar por agora"

**O que dizer**:
```
"Após criar o tenant, o sistema guia o utilizador através de um
wizard de configuração inicial em 3 passos. Este processo ajuda
a configurar tudo o que é necessário para começar a usar o sistema.

O utilizador pode sempre saltar este passo e voltar mais tarde."
```

---

### Passo 5: Step 1 - Branding
**URL**: `/onboarding/step1`

**O que fazer**:
1. Company Name: "InovSolutions Lda"
2. Upload Logo: (fazer upload de uma imagem de exemplo)
3. Primary Color: Escolher cor (ex: #3B82F6 - azul)
4. Secondary Color: Escolher cor (ex: #10B981 - verde)
5. Clicar "Próximo"

**O que dizer**:
```
"No primeiro passo, configuramos a identidade visual da empresa.
O logotipo, nome e cores são guardados nas settings do tenant e
podem ser usados em toda a aplicação para personalização.

Estas configurações ficam isoladas por tenant - cada empresa tem
o seu próprio branding."
```

**Destacar**:
- Preview em tempo real das cores (se disponível)
- Validação de ficheiros (apenas imagens)

---

### Passo 6: Step 2 - Convidar Utilizadores
**URL**: `/onboarding/step2`

**O que fazer**:
1. Adicionar primeiro utilizador:
   - Nome: "Maria Silva"
   - Email: "maria.silva@example.com"
   - Role: "Admin"
2. Clicar "Adicionar outro utilizador"
3. Adicionar segundo utilizador:
   - Nome: "João Costa"
   - Email: "joao.costa@example.com"
   - Role: "Member"
4. Clicar "Próximo"

**O que dizer**:
```
"No segundo passo, o proprietário pode convidar membros da sua equipa.
Cada utilizador recebe um convite por email e pode ter diferentes
níveis de permissão:
- Owner: Controlo total
- Admin: Gestão quase completa
- Member: Acesso básico

Os utilizadores convidados terão acesso apenas a este tenant,
garantindo isolamento total de dados."
```

**Destacar**:
- Podem ser convidados múltiplos utilizadores de uma vez
- Roles diferentes têm permissões diferentes
- Utilizadores podem estar em múltiplos tenants

---

### Passo 7: Step 3 - Configurar Permissões
**URL**: `/onboarding/step3`

**O que fazer**:
1. Mostrar permissões para "Members":
   - Marcar: "Ver Entidades", "Criar Contactos"
   - Desmarcar: "Eliminar Entidades", "Gerir Faturação"
2. Mostrar permissões para "Admins":
   - Marcar quase todas
3. (Opcional) Criar role customizada
4. Clicar "Concluir Configuração"

**O que dizer**:
```
"No terceiro passo, definimos as permissões padrão para cada tipo
de utilizador. Isto permite controlar exatamente o que cada role
pode fazer no sistema.

As permissões são organizadas por categorias: entidades, contactos,
calendário, artigos, financeiro e utilizadores.

É também possível criar roles customizadas para necessidades
específicas."
```

**Destacar**:
- Granularidade de permissões
- Permissões guardadas em tenant.settings
- Podem ser alteradas mais tarde

---

### Passo 8: Ecrã de Conclusão
**URL**: `/onboarding/complete`

**O que mostrar**:
- Mensagem de parabéns
- Lista de tarefas completadas (✓)
- Barra de progresso a 100%
- Botões: "Ir para Dashboard" ou "Ver Checklist Completa"

**O que dizer**:
```
"Onboarding concluído! As 3 tarefas obrigatórias foram completadas:
✓ Branding configurado
✓ Utilizadores convidados
✓ Permissões definidas

O tenant está agora pronto a usar. O sistema também tem tarefas
opcionais que podem ser completadas mais tarde, como configurar
artigos, criar primeiro contacto, etc."
```

---

## 📝 PARTE 4: GESTÃO DE SUBSCRIÇÕES (5-6 min)

### Passo 9: Ver Planos Disponíveis
**URL**: `/subscriptions/plans`

**O que mostrar**:
- Cards de planos lado a lado
- Exemplo de estrutura:
  - **Free** (€0/mês): 1 utilizador, funcionalidades básicas
  - **Basic** (€29/mês): 5 utilizadores, 14 dias trial, invoicing
  - **Pro** (€79/mês): 20 utilizadores, 14 dias trial, API, prioridade
  - **Enterprise** (€199/mês): Ilimitado, tudo incluído

**O que fazer**:
1. Scroll pelos planos
2. Mostrar features de cada um
3. Mostrar limits de cada um

**O que dizer**:
```
"Vamos agora ver o sistema de subscrições. Temos 4 planos configurados,
cada um com diferentes features e limites:

- Plano Free: Para testar o sistema
- Plano Basic: Para pequenas empresas (com 14 dias de trial grátis)
- Plano Pro: Para empresas em crescimento (com trial)
- Plano Enterprise: Para grandes organizações

Cada plano tem limites específicos, como número de utilizadores,
faturas por mês, espaço de armazenamento, etc."
```

**Destacar**:
- Badge "Popular" num dos planos
- Distinção entre features e limits
- Período de trial disponível

---

### Passo 10: Subscrever Plano com Free Trial
**URL**: `/subscriptions/plans` → Clicar "Começar Teste Grátis" no Basic

**O que fazer**:
1. Clicar "Começar Teste Grátis" no plano Basic
2. Confirmar subscrição
3. Redireciona para `/subscriptions`

**O que dizer**:
```
"Vou subscrever o plano Basic que tem 14 dias de trial grátis.
Durante este período, o tenant tem acesso completo às funcionalidades
do plano, sem necessidade de pagamento.

O sistema vai:
- Criar uma subscrição com status 'trialing'
- Definir trial_ends_at para daqui a 14 dias
- Ativar todas as features do plano
- Aplicar os limites de utilização"
```

---

### Passo 11: Ver Detalhes da Subscrição
**URL**: `/subscriptions`

**O que mostrar**:
- Badge de status: "Em Trial" (azul)
- Nome do plano: "Basic"
- Datas: Trial termina em X dias
- Próxima cobrança: após trial
- Botão: "Fazer Upgrade"

**Secção de Utilização**:
- Progress bars para cada feature:
  - Utilizadores: 2/5 (40%)
  - Faturas este mês: 0/100 (0%)
  - Armazenamento: 0/10 GB (0%)

**O que dizer**:
```
"No dashboard de subscrições, o utilizador vê:
1. Status atual - neste caso 'Em Trial'
2. Quantos dias faltam para o trial acabar
3. Utilização atual vs limites do plano
4. Opções para fazer upgrade, downgrade ou cancelar"
```

**Destacar**:
- Sistema de notificações antes do trial acabar (dia 11/14)
- Utilização em tempo real
- Acesso fácil a mudar de plano

---

### Passo 12: Fazer Upgrade (durante trial)
**URL**: `/subscriptions/plans` → Clicar "Fazer Upgrade" no Pro

**O que fazer**:
1. Ir para `/subscriptions/plans`
2. Clicar "Fazer Upgrade" no plano Pro
3. Confirmar upgrade
4. Mostrar mensagem de sucesso

**O que dizer**:
```
"Vou agora fazer upgrade do plano Basic para Pro. Como ainda estamos
em trial, não há cobrança imediata, mas o trial é removido.

O sistema:
- Atualiza o plano imediatamente
- Aumenta os limites (de 5 para 20 utilizadores)
- Adiciona novas features (API access, priority support)
- Calcula valor pró-rata a pagar
- Atualiza próxima data de cobrança"
```

---

### Passo 13: Ver Subscrição Após Upgrade
**URL**: `/subscriptions`

**O que mostrar**:
- Badge: "Ativa" (verde)
- Plano: "Pro"
- Limites atualizados:
  - Utilizadores: 2/20 (10%)
  - Faturas: 0/500 (0%)
  - Armazenamento: 0/50 GB (0%)
- Features novas disponíveis

**O que dizer**:
```
"A subscrição está agora ativa no plano Pro. Reparem como os limites
aumentaram automaticamente e novas features foram ativadas."
```

---

### Passo 14: Fazer Downgrade
**URL**: `/subscriptions/plans` → Clicar "Fazer Downgrade" no Basic

**O que fazer**:
1. Clicar "Fazer Downgrade" no plano Basic
2. Confirmar downgrade
3. Mostrar mensagem: "Downgrade agendado para [data]"

**O que dizer**:
```
"Ao contrário do upgrade que é imediato, o downgrade é agendado para
o final do período de cobrança atual.

Isto significa:
- O utilizador continua a pagar o plano Pro até ao fim do período
- No próximo ciclo de cobrança, muda automaticamente para Basic
- Pode cancelar o downgrade a qualquer momento antes de acontecer
- Não há reembolso pelo tempo restante no plano superior"
```

---

### Passo 15: Ver Downgrade Agendado
**URL**: `/subscriptions`

**O que mostrar**:
- Badge: "Ativa" (ainda no Pro)
- Alert amarelo: "Downgrade agendado para Basic em [data]"
- Botão: "Cancelar Downgrade Agendado"

**O que fazer**:
1. Mostrar o alert
2. Clicar "Cancelar Downgrade Agendado"
3. Confirmar cancelamento

**O que dizer**:
```
"O utilizador pode ver que tem um downgrade agendado e pode
cancelá-lo se mudar de ideias. Vou cancelar agora para continuarmos
com o plano Pro."
```

---

### Passo 16: Cancelar Subscrição (Imediatamente)
**URL**: `/subscriptions` → Clicar "Cancelar Subscrição"

**O que fazer**:
1. Clicar "Cancelar Subscrição"
2. Selecionar: "Cancelar imediatamente" (em vez de "no fim do período")
3. Confirmar cancelamento
4. Mostrar crédito criado

**O que dizer**:
```
"O utilizador pode cancelar a subscrição de duas formas:

1. **No fim do período**: Mantém acesso até ao fim do que já pagou
2. **Imediatamente**: Perde acesso agora, mas recebe crédito pró-rata

Vou escolher 'imediatamente'. O sistema:
- Calcula quantos dias faltam no período atual
- Calcula o valor proporcional a devolver
- Cria um crédito na conta do tenant
- O crédito expira em 12 meses
- Status muda para 'Cancelada'"
```

---

### Passo 17: Ver Créditos Disponíveis
**URL**: `/subscriptions/history` ou dashboard com secção de créditos

**O que mostrar**:
- Secção "Créditos Disponíveis"
- Lista de créditos:
  - Tipo: "Cancellation Credit"
  - Valor: €XX.XX
  - Expira em: [data]
  - Status: "Pending"

**O que dizer**:
```
"Os créditos de cancelamento ficam disponíveis na conta do tenant
por 12 meses. Podem ser usados automaticamente em futuras subscrições.

O sistema usa FIFO - os créditos que expiram primeiro são usados
primeiro."
```

---

### Passo 18: Reativar Subscrição
**URL**: `/subscriptions` → Clicar "Reativar Subscrição"

**O que fazer**:
1. Clicar "Reativar Subscrição" ou "Retomar"
2. Escolher plano (ou manter o anterior)
3. Confirmar
4. Mostrar que crédito foi aplicado

**O que dizer**:
```
"Subscrições canceladas podem ser reativadas. Se houver créditos
disponíveis, são aplicados automaticamente à nova subscrição.

Neste caso, o crédito de €XX foi aplicado, reduzindo o valor a pagar."
```

---

### Passo 19: Ver Histórico e Audit Logs
**URL**: `/subscriptions/history`

**O que mostrar**:
- Timeline de eventos:
  - ✓ Subscrição criada (Basic, Trial)
  - ✓ Upgrade para Pro (€XX pró-rata)
  - ✓ Downgrade agendado para Basic
  - ✓ Downgrade cancelado
  - ✓ Subscrição cancelada imediatamente (€XX crédito)
  - ✓ Subscrição reativada

- Para cada evento mostrar:
  - Data/hora
  - Utilizador que fez a ação
  - Detalhes (planos, valores, status)

**O que dizer**:
```
"Todo o histórico de subscrições é registado em audit logs. Isto
permite:
- Rastreabilidade total de todas as mudanças
- Saber quem fez cada ação e quando
- Ver valores cobrados, créditos dados, etc.
- Resolver disputas ou questões de faturação

Cada evento regista: data, utilizador, IP, user agent, valores,
planos anteriores e novos."
```

---

### Passo 20: Dashboard de Utilização
**URL**: `/subscriptions/dashboard`

**O que mostrar**:
- Gráficos de utilização ao longo do tempo:
  - Utilizadores (linha temporal)
  - Faturas criadas por mês (bar chart)
  - Armazenamento usado (área chart)

- Cards de resumo:
  - Utilizadores: X/20
  - Faturas este mês: X/500
  - Próxima cobrança: [data]
  - Créditos disponíveis: €XX

**O que dizer**:
```
"O dashboard de subscrições mostra a evolução da utilização ao longo
do tempo. Isto ajuda o cliente a:
- Ver se está a aproximar-se dos limites
- Decidir se precisa fazer upgrade
- Prever custos futuros

Os dados são registados diariamente na tabela subscription_usage_history."
```

---

## 📝 PARTE 5: MULTI-TENANT - CRIAÇÃO E NAVEGAÇÃO (3-4 min)

### Passo 21: Criar Segundo Tenant
**URL**: `/tenants/create` (navegar manualmente ou via menu)

**O que fazer**:
1. Ir para criação de novo tenant
2. Nome: "TechStart Unipessoal"
3. Criar tenant
4. Sistema troca automaticamente para o novo tenant

**O que dizer**:
```
"Um utilizador pode criar múltiplos tenants. Isto é útil quando:
- Gere várias empresas
- Tem diferentes projetos ou departamentos
- Quer separar ambientes (produção vs teste)

Cada tenant é completamente isolado:
- Dados separados
- Subscrições independentes
- Utilizadores diferentes
- Configurações próprias"
```

---

### Passo 22: Configurar Segundo Tenant
**URL**: Wizard de onboarding do novo tenant

**O que fazer**:
1. Passar rapidamente pelo wizard (fast forward)
   - Branding diferente (outras cores)
   - Sem convidar utilizadores (saltar)
   - Permissões padrão
2. Ir para dashboard

**O que dizer**:
```
"Este segundo tenant passa pelo mesmo processo de onboarding, mas
pode ter configurações completamente diferentes do primeiro.

Vou configurá-lo rapidamente com branding diferente para vermos
a separação."
```

---

### Passo 23: Navegar Entre Tenants
**URL**: Menu de seleção de tenants (sidebar ou header)

**O que mostrar**:
- Menu dropdown ou sidebar com lista de tenants
- Mostrar os 2 tenants:
  - InovSolutions Lda
  - TechStart Unipessoal

**O que fazer**:
1. Clicar no nome do tenant atual (TechStart)
2. Ver lista de tenants disponíveis
3. Clicar em "InovSolutions Lda"
4. Página recarrega com contexto do outro tenant

**O que dizer**:
```
"A navegação entre tenants é simples e rápida. O utilizador clica
no nome do tenant atual e escolhe outro da lista.

Quando muda de tenant:
- O current_tenant_id é atualizado na sessão
- Todos os dados mostrados mudam para o novo tenant
- Subscrição, utilizadores, configurações - tudo é do novo contexto
- Não é necessário novo login"
```

---

### Passo 24: Mostrar Isolamento de Dados
**O que fazer**:
1. Mostrar dashboard do InovSolutions:
   - Cores: azul e verde
   - Subscrição: Pro (ativa)
   - Utilizadores: 3 (Maria, João, + owner)

2. Trocar para TechStart:
   - Cores diferentes
   - Subscrição: Free (ou sem subscrição)
   - Utilizadores: 1 (apenas owner)

**O que dizer**:
```
"Reparem como os dados são completamente diferentes:

Tenant 1 (InovSolutions):
- Plano Pro, subscrição ativa
- 3 utilizadores
- Branding azul/verde
- Dados de entidades, faturas, etc.

Tenant 2 (TechStart):
- Plano Free
- Apenas 1 utilizador
- Branding diferente
- Dados completamente separados

Esta é a essência do multi-tenancy: isolamento total com partilha
de infraestrutura."
```

---

### Passo 25: Convidar Utilizador que Já Existe
**URL**: Onboarding ou gestão de utilizadores do TechStart

**O que fazer**:
1. No tenant TechStart, convidar "maria.silva@example.com"
2. Sistema deteta que utilizador já existe
3. Apenas adiciona relação ao novo tenant
4. Maria agora tem acesso a 2 tenants

**O que dizer**:
```
"Um utilizador pode ter acesso a múltiplos tenants. Vou convidar
a Maria Silva para o segundo tenant.

Como ela já existe no sistema (está no InovSolutions), o sistema:
- Não cria novo utilizador
- Apenas adiciona a relação na tabela user_tenant
- Maria pode agora trocar entre os 2 tenants

Isto é útil para:
- Consultores que trabalham com várias empresas
- Gestores de múltiplos projetos
- Suporte técnico que precisa aceder a vários clientes"
```

---

### Passo 26: Permissões Diferentes em Cada Tenant
**O que mostrar**:
- Maria no tenant InovSolutions: role "Admin"
- Maria no tenant TechStart: role "Member"

**O que dizer**:
```
"Um utilizador pode ter diferentes roles em diferentes tenants:
- No InovSolutions, Maria é Admin (pode gerir quase tudo)
- No TechStart, Maria é Member (acesso limitado)

As permissões são guardadas na relação user_tenant, por isso são
independentes em cada tenant."
```

---

## 📝 PARTE 6: FUNCIONALIDADES TÉCNICAS E CONCLUSÃO (2-3 min)

### Passo 27: Destacar Funcionalidades Técnicas

**O que dizer** (pode mostrar código brevemente ou apenas mencionar):
```
"Por trás destas funcionalidades, temos uma arquitetura robusta:

**Backend:**
- Models: Tenant, Subscription, Plan, OnboardingChecklist, TenantCredit
- Services: SubscriptionService, CreditService, AuditService
- Controllers RESTful com validação completa

**Isolamento de Dados:**
- Todas as queries filtram por current_tenant_id
- Middleware garante que utilizador tem acesso ao tenant
- API recebe sempre tenant_id para validação

**Sistema de Créditos:**
- Cálculo automático de valores pró-rata
- FIFO para aplicação de créditos
- Expiração automática após 12 meses

**Audit Logging:**
- Todos os eventos de subscrição registados
- IP, user agent, utilizador, timestamps
- Metadata JSON para informação extra

**Usage Tracking:**
- Registo em tempo real de utilização
- Snapshots diários para histórico
- Gráficos de tendências

**Billing Rules:**
- Upgrade: cobrança imediata pró-rata
- Downgrade: agendado para próximo período
- Cancelamento: com ou sem reembolso
- Trial: conversão automática ou expiração"
```

---

### Passo 28: Conclusão e Recap

**O que dizer**:
```
"Vamos recapitular o que vimos hoje:

✅ **Multi-Tenancy Completo:**
   - Criação de múltiplos tenants por utilizador
   - Navegação fácil entre tenants
   - Isolamento total de dados e permissões

✅ **Onboarding Intuitivo:**
   - Wizard guiado em 3 passos
   - Branding, utilizadores e permissões
   - Checklist de tarefas opcionais

✅ **Subscrições Flexíveis:**
   - Múltiplos planos com features e limits
   - Free trial de 14 dias
   - Upgrade imediato com pró-rata
   - Downgrade agendado
   - Sistema de créditos

✅ **Gestão Transparente:**
   - Dashboard de utilização em tempo real
   - Histórico completo com audit logs
   - Notificações antes do fim do trial
   - Créditos com expiração

✅ **Segurança e Isolamento:**
   - Cada tenant é completamente isolado
   - Permissões granulares por role
   - Audit trail completo
   - API sempre validada por tenant

Este sistema está pronto para produção e pode escalar para centenas
ou milhares de tenants sem problemas de performance ou segurança.

Obrigado por assistirem!"
```

---

## 🎬 Dicas de Gravação

### Durante o Vídeo:
1. **Fale devagar e claramente** - deixe tempo para o espectador absorver
2. **Use o rato para destacar** elementos importantes no ecrã
3. **Pause brevemente** depois de ações importantes (criar, salvar, etc.)
4. **Mostre validações** - erros propositais para mostrar robustez
5. **Destaque mensagens de sucesso** - "Tenant criado!", "Upgrade concluído!"

### Edição:
1. Adicione **zoom** em elementos importantes (botões, formulários)
2. Use **slow motion** em transições rápidas
3. Adicione **legendas** com termos técnicos
4. Destaque com **setas ou círculos** elementos importantes
5. Corte partes lentas (ex: loading, typing longo)

### Música/Som:
- Use música de fundo suave durante explicações
- Baixe volume da música quando falar
- Sons de "click" ou "success" para feedback positivo

---

## 📊 Checklist Final

### Antes de Publicar:
- [ ] Vídeo tem introdução clara
- [ ] Todos os 6 passos principais foram cobertos
- [ ] Demonstração fluiu sem problemas técnicos
- [ ] Áudio está claro e sem ruído
- [ ] Duração entre 15-20 minutos
- [ ] Legendas ou captions adicionadas (opcional)
- [ ] Thumbnail atrativo criado
- [ ] Descrição do vídeo com timestamps

### Timestamps Sugeridos para Descrição:
```
00:00 - Introdução
01:30 - Criação do Primeiro Tenant
04:00 - Wizard de Onboarding
08:30 - Gestão de Subscrições
14:00 - Multi-Tenant Navigation
17:00 - Funcionalidades Técnicas
19:00 - Conclusão
```

---

## 🛠️ Resolução de Problemas Durante Gravação

### Se algo correr mal:
1. **Erro ao criar tenant**: Verificar BD, migrations
2. **Wizard não avança**: Console do browser (F12) para ver erros
3. **Subscrição não cria**: Verificar se há planos na BD
4. **Upload de logo falha**: Verificar permissões storage/
5. **Troca de tenant não funciona**: Limpar cache/session

### Scripts Úteis:
```bash
# Resetar BD para demonstração limpa
php artisan migrate:fresh --seed

# Criar planos de exemplo
php artisan db:seed --class=PlansSeeder

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

**Boa sorte com a gravação! 🎥🚀**
