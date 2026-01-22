# ⚠️ Como Resolver Erro do Build (Ziggy)

## 🐛 Erro Atual

```
Could not resolve "../../vendor/tightenco/ziggy" from "resources/js/app.js"
```

Este erro acontece porque o package Ziggy (usado para rotas no JavaScript) não está a ser encontrado.

---

## ✅ Solução

### Opção 1: Executar Composer Install

O Ziggy é instalado via Composer (não npm):

```bash
composer install
```

Depois:

```bash
npm run build
```

---

### Opção 2: Executar Script Completo

Execute este comando que faz tudo:

```bash
composer install && npm run build
```

---

### Opção 3: Usar npm run dev (Recomendado durante desenvolvimento)

Se está a desenvolver, use modo dev em vez de build:

```bash
npm run dev
```

Deixe este comando a correr. Ele recompila automaticamente quando altera ficheiros.

---

## 📝 Menu de Subscrições Adicionado

✅ **Já foi adicionado ao código!** Quando fizer o build com sucesso, verá:

### Menu Desktop:
```
Documentos | Subscrições ▼ | Sistema ▼ | Configurações ▼
                ├── Ver Planos
                ├── Minha Subscrição
                ├── Dashboard
                └── Histórico
```

### Menu Mobile:
```
Subscrições
  ├── Ver Planos
  ├── Minha Subscrição
  ├── Dashboard
  └── Histórico
```

---

## 🚀 Após Resolver

1. Execute: `composer install && npm run build`
2. Limpe cache do browser (Ctrl+Shift+R)
3. Recarregue a aplicação
4. ✅ Menu "Subscrições" deve aparecer!

---

## 📍 Acesso Direto às URLs (Alternativa)

Se não quiser fazer build agora, pode aceder diretamente:

```
http://localhost:8000/subscriptions/plans      - Ver Planos
http://localhost:8000/subscriptions             - Minha Subscrição
http://localhost:8000/subscriptions/dashboard   - Dashboard
http://localhost:8000/subscriptions/history     - Histórico
```

---

**Quando fizer build, o menu aparecerá automaticamente! 🎉**
