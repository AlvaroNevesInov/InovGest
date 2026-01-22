# 🔧 Como Resolver o Erro da Checklist

## 🐛 Problema

Erro: `TypeError: Cannot read properties of undefined (reading 'length')`

Isto acontece porque o browser ainda está a usar a **versão antiga** dos ficheiros JavaScript, mesmo depois de fazer rebuild.

---

## ✅ Solução Passo a Passo

### 1️⃣ Rebuild Completo (Force)

Execute este comando:

```bash
./force_rebuild.sh
```

Este script vai:
- ✅ Limpar cache do Vite
- ✅ Limpar pasta `public/build`
- ✅ Fazer build completo dos assets
- ✅ Verificar se build foi bem-sucedido

**Tempo estimado**: 30-60 segundos

---

### 2️⃣ Limpar Cache do Browser

É **ESSENCIAL** limpar a cache do browser após o rebuild!

#### Chrome / Edge / Brave:
1. Pressione **Ctrl + Shift + Delete** (ou **Cmd + Shift + Delete** no Mac)
2. Selecione **"Cached images and files"** / **"Imagens e ficheiros em cache"**
3. Período: **"Last hour"** / **"Última hora"** (ou "All time" para garantir)
4. Clique **"Clear data"** / **"Limpar dados"**

#### Alternativa Rápida (Hard Reload):
- **Ctrl + Shift + R** (Windows/Linux)
- **Cmd + Shift + R** (Mac)

Pressione **5 vezes seguidas** para garantir!

---

### 3️⃣ Teste Novamente

1. Vá para o ecrã de conclusão do onboarding
2. Clique em **"Ver Checklist"**
3. **Deve funcionar sem erros!**

---

## 🔍 Verificação

Se ainda houver erros, verifique:

### A. Ficheiros foram gerados?
```bash
ls -la public/build/
```

Deve ver ficheiros como:
- `manifest.json`
- `AuthenticatedLayout-XXXXX.js`
- `Checklist-XXXXX.js`

### B. Build teve erros?
```bash
cat /tmp/last-build.log
```

Se houver erros de compilação, resolva-os antes de continuar.

### C. Cache do browser foi limpa?

Abra DevTools (F12) → Network → **"Disable cache"**

Depois recarregue a página.

---

## 🚨 Se AINDA Não Funcionar

### Opção 1: Modo Desenvolvimento

Em vez de usar build, use modo dev:

```bash
npm run dev
```

Deixe este comando a correr numa janela de terminal separada.
Agora qualquer alteração nos ficheiros `.vue` será compilada automaticamente!

### Opção 2: Abrir em Janela Anónima/Privada

1. Abra uma **janela anónima** (Ctrl+Shift+N)
2. Faça login novamente
3. Tente aceder à checklist

Se funcionar em anónima → problema é cache
Se não funcionar em anónima → problema é código

---

## 📋 Checklist de Debug

- [ ] `force_rebuild.sh` executado sem erros
- [ ] Pasta `public/build/` contém ficheiros
- [ ] Cache do browser foi limpa (Ctrl+Shift+Delete)
- [ ] Hard reload feito 5 vezes (Ctrl+Shift+R)
- [ ] DevTools → Console sem erros
- [ ] DevTools → Network → ficheiros .js carregados (200 OK)

---

## 💡 Dica Pro

Para evitar este problema no futuro:

**Durante desenvolvimento, use sempre:**
```bash
npm run dev
```

**Apenas para produção/deploy, use:**
```bash
npm run build
```

---

## 🆘 Ainda Com Problemas?

Se nada funcionar, partilhe:

1. **Screenshot** do console (F12 → Console)
2. **Screenshot** do Network tab (F12 → Network)
3. **Output** do comando: `./force_rebuild.sh`

---

## 🎯 Alterações Feitas no Código

Para referência, estas correções já foram aplicadas:

### `AuthenticatedLayout.vue`
```vue
<!-- Antes (ERRADO) -->
<div v-if="$page.props.tenant.companies.length > 0">

<!-- Depois (CORRETO) -->
<div v-if="$page.props.tenant?.companies && $page.props.tenant.companies.length > 0">
```

### `Checklist.vue`
```vue
<!-- Antes (ERRADO) -->
{{ progress.completion_percentage }}%

<!-- Depois (CORRETO) -->
{{ progress?.completion_percentage || 0 }}%
```

### `TenantOnboardingController.php`
```php
// Cria tarefas automaticamente se não existirem
$checklistCount = OnboardingChecklist::where('tenant_id', $tenant->id)->count();
if ($checklistCount === 0) {
    OnboardingChecklist::createDefaultTasksForTenant($tenant->id);
}
```

**Todas estas alterações precisam ser compiladas para funcionar!**

---

**Boa sorte! 🍀**
