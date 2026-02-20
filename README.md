# Sistema de Gestao de Produtos e Fornecedores

Sistema web para gestao de Produtos, Fornecedores, Vinculos e Pedidos.

## Stack

- **Backend:** Laravel 11 (MVC + Service Layer)
- **Frontend:** Vue 3 (Composition API) + Inertia.js v2
- **CSS:** Tailwind CSS
- **Banco:** MySQL
- **Filas:** Redis + Laravel Jobs
- **Build:** Vite

## Pre-requisitos

- PHP >= 8.1
- Composer
- Node.js >= 18
- MySQL
- Redis (Docker ou Memurai)

## Instalacao

```bash
# Clonar o repositorio
git clone <url-do-repo>
cd product-supplier-management

# Instalar dependencias PHP
composer install

# Instalar dependencias JS
npm install

# Copiar .env e gerar key
cp .env.example .env
php artisan key:generate
```

## Configuracao

Editar o arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_supplier_management
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## Redis no Windows

**Opcao 1 - Docker (recomendado):**
```bash
docker run -d --name redis -p 6379:6379 redis:alpine
```

**Opcao 2 - Memurai:**
Baixar em https://www.memurai.com/ e instalar.

## Banco de Dados

```bash
# Criar o banco MySQL
mysql -u root -e "CREATE DATABASE product_supplier_management"

# Executar migrations
php artisan migrate

# Popular com dados de teste
php artisan db:seed
```

## Executar

```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend (dev)
npm run dev

# Terminal 3 - Queue worker (para jobs bulk)
php artisan queue:work redis
```

Acessar: http://localhost:8000

**Login de teste:** admin@example.com / password

## Funcionalidades

### Fornecedores
- CRUD completo com soft delete
- Busca por nome, CNPJ, e-mail
- Filtro por status (ativo/inativo)
- Mascara de CNPJ e telefone

### Produtos
- CRUD completo com soft delete
- Busca por nome, codigo interno, descricao
- Filtro por status
- Codigo interno unico

### Vinculos Produto-Fornecedor
- Painel duplo: vinculados vs disponiveis
- Vinculacao/desvinculacao individual
- Operacoes bulk assincronas (via Redis jobs)
- Select-all para operacoes em massa

### Pedidos
- Criacao com selecao de fornecedor e data
- Adicao/remocao de itens com validacao
- Calculo automatico de totais
- Maquina de estado: Aberto -> Processando -> Concluido/Cancelado
- Bloqueio de edicao em pedidos concluidos/cancelados
- Historico de status com timeline visual
- Filtros por status, fornecedor e busca textual

### Dashboard
- Cards de resumo (fornecedores, produtos, pedidos, faturamento)
- Tabela de pedidos recentes

## Estrutura do Projeto

```
app/
  Enums/          # EntityStatus, OrderStatus
  Http/
    Controllers/  # Resource controllers
    Middleware/   # Inertia flash messages
    Requests/     # Form validation
  Jobs/           # Bulk link/unlink jobs
  Models/         # Eloquent models
  Services/       # Business logic layer
resources/js/
  Components/     # Shared Vue components
  Composables/    # useFilters
  Layouts/        # AuthenticatedLayout (sidebar)
  Pages/          # Inertia pages
```
