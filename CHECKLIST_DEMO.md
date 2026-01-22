# ✅ Checklist Rápido para Demonstração

## 🚀 Antes de Começar a Gravar

### Preparação Técnica
- [ ] Executar `./preparar_demo.sh` para resetar ambiente
- [ ] Servidor Laravel a correr: `php artisan serve`
- [ ] Browser limpo (sem sessões antigas, cache limpo)
- [ ] Verificar que planos existem: visitar `/subscriptions/plans` após login
- [ ] Preparar imagem de logo para upload (PNG/JPG, ~200x200px)

### Preparação de Gravação
- [ ] Microfone testado e funcional
- [ ] Ecrã limpo (fechar tabs desnecessárias, notificações off)
- [ ] Resolução adequada (1920x1080 ou 1280x720)
- [ ] Software de gravação pronto (OBS, Camtasia, etc.)
- [ ] Notas/guia impresso ou num segundo monitor

### Dados Preparados
- [ ] Email de teste: `joao.santos@example.com`
- [ ] Password de teste memorizada
- [ ] Nome do Tenant 1: `InovSolutions Lda`
- [ ] Nome do Tenant 2: `TechStart Unipessoal`
- [ ] Emails para convidar: `maria.silva@example.com`, `joao.costa@example.com`
- [ ] Cores preparadas: #3B82F6 (azul), #10B981 (verde)

---

## 🎬 Durante a Gravação

### Dicas de Apresentação
- [ ] Falar devagar e claramente
- [ ] Pausar após ações importantes
- [ ] Usar rato para destacar elementos
- [ ] Explicar "porquê" e não apenas "o quê"
- [ ] Manter energia e entusiasmo

### Checklist de Conteúdo
- [ ] ✅ PARTE 1: Introdução (1-2 min)
- [ ] ✅ PARTE 2: Criação Primeiro Tenant (3-4 min)
- [ ] ✅ PARTE 3: Onboarding Wizard (4-5 min)
  - [ ] Step 1: Branding
  - [ ] Step 2: Utilizadores
  - [ ] Step 3: Permissões
- [ ] ✅ PARTE 4: Gestão Subscrições (5-6 min)
  - [ ] Ver planos
  - [ ] Subscrever com trial
  - [ ] Fazer upgrade
  - [ ] Fazer downgrade
  - [ ] Cancelar e reativar
  - [ ] Ver créditos e histórico
- [ ] ✅ PARTE 5: Multi-Tenant (3-4 min)
  - [ ] Criar segundo tenant
  - [ ] Navegar entre tenants
  - [ ] Mostrar isolamento
- [ ] ✅ PARTE 6: Conclusão (1 min)

---

## 🎯 Pontos-Chave a Destacar

### Multi-Tenancy
- ✨ Isolamento total de dados
- ✨ Um utilizador pode ter múltiplos tenants
- ✨ Diferentes roles em cada tenant
- ✨ Troca de tenant sem novo login

### Onboarding
- ✨ Wizard guiado em 3 passos
- ✨ Pode ser saltado e retomado depois
- ✨ Checklist de tarefas opcionais
- ✨ Configurações independentes por tenant

### Subscrições
- ✨ Free trial de 14 dias
- ✨ Upgrade imediato com pró-rata
- ✨ Downgrade agendado (não imediato)
- ✨ Cancelamento com créditos
- ✨ Sistema de créditos FIFO com expiração

### Features Técnicas
- ✨ Audit logging completo
- ✨ Usage tracking em tempo real
- ✨ Dashboard com gráficos
- ✨ API sempre validada por tenant
- ✨ Billing rules automáticas

---

## ⚠️ Problemas Comuns e Soluções

### Se algo correr mal durante gravação:

**Problema**: Tenant não cria
- **Solução**: Verificar console browser (F12), verificar BD

**Problema**: Upload de logo falha
- **Solução**: Verificar permissões `storage/app/public/`, executar `php artisan storage:link`

**Problema**: Planos não aparecem
- **Solução**: Executar `php artisan db:seed --class=PlanSeeder`

**Problema**: Wizard não avança
- **Solução**: Verificar validação de formulários, console do browser

**Problema**: Subscrição não cria
- **Solução**: Verificar se planos existem na BD, verificar relações

**Problema**: Troca de tenant não funciona
- **Solução**: Limpar sessão, verificar relação user_tenant

### Comandos Úteis de Emergência:
```bash
# Limpar tudo
php artisan cache:clear && php artisan config:clear

# Resetar apenas storage
php artisan storage:link

# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Verificar planos
php artisan tinker --execute="App\Models\Plan::all(['name', 'price'])"

# Verificar tenants
php artisan tinker --execute="App\Models\Tenant::all(['name', 'slug'])"
```

---

## 📹 Após a Gravação

### Edição
- [ ] Remover partes lentas/erros
- [ ] Adicionar zoom em elementos importantes
- [ ] Adicionar legendas/subtítulos
- [ ] Música de fundo suave
- [ ] Transições suaves entre secções
- [ ] Criar thumbnail atrativo

### Publicação
- [ ] Título descritivo
- [ ] Descrição detalhada com timestamps
- [ ] Tags relevantes
- [ ] Link para documentação/repositório (se aplicável)

### Timestamps Exemplo:
```
00:00 - Introdução ao Multi-Tenancy
01:30 - Criação do Primeiro Tenant
04:00 - Wizard de Onboarding (Branding, Users, Permissions)
08:30 - Sistema de Subscrições
10:00 - Free Trial e Trial Conversion
11:30 - Upgrade e Downgrade de Planos
13:00 - Sistema de Créditos e Billing
14:30 - Multi-Tenant Navigation
17:00 - Isolamento de Dados e Permissões
18:00 - Features Técnicas e Arquitetura
19:30 - Conclusão e Recap
```

---

## 💡 Dicas Extra

### Para Vídeo Mais Profissional:
1. **Introdução visual**: Criar slide de abertura com logo e título
2. **B-roll**: Screenshots de código relevante (models, services)
3. **Diagramas**: Mostrar arquitetura (tenant → user → subscription)
4. **Comparações**: Mostrar "antes vs depois" de features
5. **Call-to-action**: No final, convidar a testar/contribuir

### Aspectos a Mencionar:
- **Escalabilidade**: Sistema pronto para centenas de tenants
- **Segurança**: Isolamento de dados, validações, audit logs
- **UX**: Fluxo intuitivo, mensagens claras, feedback visual
- **Flexibilidade**: Configurável, extensível, modular
- **Produção-ready**: Testes, validações, error handling

---

## 🎓 Scripts Prontos (Copy-Paste)

### Abertura:
```
"Olá! Bem-vindos à demonstração do sistema multi-tenant que desenvolvemos
para o InovGest. Hoje vou mostrar como um utilizador pode criar múltiplos
tenants, gerir subscrições, fazer upgrades e downgrades, e muito mais.
Vamos começar!"
```

### Transição para Onboarding:
```
"Agora que criámos o nosso primeiro tenant, o sistema vai guiar-nos através
de um wizard de configuração inicial. Este processo é opcional, mas ajuda a
configurar tudo rapidamente."
```

### Transição para Subscrições:
```
"Com o tenant configurado, vamos explorar o sistema de subscrições. Temos
vários planos disponíveis, cada um com diferentes features e limites."
```

### Transição para Multi-Tenant:
```
"Uma das funcionalidades mais poderosas deste sistema é a capacidade de um
utilizador gerir múltiplos tenants. Vamos criar um segundo tenant e ver como
a navegação funciona."
```

### Fecho:
```
"E é isto! Vimos como o sistema multi-tenant permite criar, gerir e navegar
entre múltiplos tenants, com subscrições flexíveis, onboarding guiado, e
isolamento total de dados. Obrigado por assistirem!"
```

---

**Boa sorte! 🚀🎬**
