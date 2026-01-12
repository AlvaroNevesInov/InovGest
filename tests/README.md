# Tests Documentation

Este projeto inclui testes automatizados tanto para backend (PHP/Laravel) quanto para frontend (Vue/JavaScript).

## Testes de Backend (PHPUnit)

### Estrutura

- **Feature Tests** (`tests/Feature/`): Testes de integração que testam fluxos completos da aplicação
- **Unit Tests** (`tests/Unit/`): Testes unitários que testam componentes isolados

### Testes Implementados

#### Feature Tests:
- ✅ **EntityTest**: CRUD de Entidades (Clientes/Fornecedores)
  - Criação, edição, eliminação
  - Validação de NIF único
  - Filtros e pesquisa
  - Cifragem de dados sensíveis

- ✅ **ContactTest**: Gestão de Contactos
  - CRUD completo
  - Relações com entidades

- ✅ **ProposalTest**: Gestão de Propostas
  - Criação e edição
  - Cálculo de totais
  - Fecho de propostas
  - Conversão para encomendas
  - Geração de PDF

- ✅ **ArticleTest**: Gestão de Artigos
  - CRUD completo
  - Upload de fotos
  - Validação de referência única

- ✅ **CalendarTest**: Gestão de Calendário
  - Criação e edição de eventos
  - Filtros por utilizador e tipo
  - Permissões de acesso

- ✅ **AuthenticationTest**: Autenticação
  - Login e logout
  - 2FA (Two-Factor Authentication)
  - Redirecionamentos

#### Unit Tests:
- ✅ **EntityModelTest**: Model Entity
  - Cifragem de campos sensíveis
  - Relações (Country, Contacts)
  - Scopes (clients, suppliers, active)
  - Geração de números incrementais

### Executar Testes de Backend

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter EntityTest

# Executar com cobertura
php artisan test --coverage

# Executar apenas Feature tests
php artisan test --testsuite Feature

# Executar apenas Unit tests
php artisan test --testsuite Unit
```

### Configuração

Os testes utilizam SQLite em memória (configurado em `phpunit.xml`) para maior velocidade e isolamento.

## Testes de Frontend (Vitest)

### Estrutura

- **Component Tests** (`tests/Vue/Components/`): Testes de componentes Vue

### Testes Implementados

- ✅ **Button.spec.js**: Componente Button
  - Renderização
  - Variantes (default, destructive, outline)
  - Tamanhos (sm, lg)
  - Estados (disabled)
  - Eventos (click)

- ✅ **Input.spec.js**: Componente Input
  - Renderização
  - v-model binding
  - Atributos (type, placeholder, disabled)
  - Classes customizadas

- ✅ **NavLink.spec.js**: Componente NavLink
  - Estado ativo/inativo
  - Classes de alinhamento
  - Integração com Inertia

### Executar Testes de Frontend

```bash
# Executar todos os testes
npm run test

# Executar com UI interativa
npm run test:ui

# Executar com cobertura
npm run test:coverage

# Modo watch (re-executar ao salvar)
npm run test -- --watch
```

### Configuração

Os testes utilizam:
- **Vitest**: Framework de testes rápido
- **Vue Test Utils**: Utilitários para testar componentes Vue
- **Happy DOM**: Ambiente DOM leve para testes

Configuração em `vitest.config.js`.

## Boas Práticas

### Backend:
1. Use `RefreshDatabase` trait para resetar o banco de dados entre testes
2. Use factories para criar dados de teste
3. Teste validações, permissões e regras de negócio
4. Use nomes descritivos nos métodos de teste (`it_can_create_an_entity`)

### Frontend:
1. Teste comportamento, não implementação
2. Use `mount()` para testes de integração, `shallowMount()` para testes unitários
3. Mock dependências externas (APIs, rotas)
4. Teste interações do utilizador (clicks, inputs)

## CI/CD

Os testes devem ser executados automaticamente em pipelines de CI/CD antes de deploy.

```yaml
# Exemplo para GitHub Actions
- name: Run Backend Tests
  run: php artisan test

- name: Run Frontend Tests
  run: npm run test
```

## Cobertura de Código

### Backend
```bash
php artisan test --coverage --min=80
```

### Frontend
```bash
npm run test:coverage
```

A cobertura mínima recomendada é de 80%.

## Adicionar Novos Testes

### Backend (Feature Test):
```bash
php artisan make:test NomeDoControllerTest
```

### Backend (Unit Test):
```bash
php artisan make:test NomeDoModelTest --unit
```

### Frontend:
Criar arquivo em `tests/Vue/Components/NomeDoComponente.spec.js`

## Debugging

### Backend:
```bash
# Executar um teste específico com output detalhado
php artisan test --filter test_method_name --verbose
```

### Frontend:
```bash
# Executar com debug
npm run test -- --reporter=verbose
```

## Recursos

- [Laravel Testing](https://laravel.com/docs/11.x/testing)
- [Vitest Documentation](https://vitest.dev/)
- [Vue Test Utils](https://test-utils.vuejs.org/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
