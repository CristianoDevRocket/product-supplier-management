# DECISOES.md - Decisoes Tecnicas e Arquiteturais

## 1. Stack Tecnologica

### Laravel 11 + Inertia.js v2 + Vue 3
Escolhi a stack Laravel + Inertia + Vue por oferecer uma experiencia SPA completa sem a complexidade de uma API REST separada. O Inertia age como "cola" entre o backend e o frontend, eliminando a necessidade de serializar respostas JSON e gerenciar estado no cliente.

### Tailwind CSS
Utilizado por permitir rapida prototipagem com classes utilitarias, resultando em componentes visuais consistentes sem necessidade de escrever CSS customizado.

### MySQL
Banco relacional robusto, ideal para o modelo de dados com multiplos relacionamentos (N:N, 1:N) e constraints de integridade referencial (foreign keys).

### Redis + Predis
Redis para filas assincronas (bulk operations). Predis (cliente PHP puro) foi escolhido para evitar dependencia da extensao phpredis compilada, facilitando setup em Windows/XAMPP.

## 2. Arquitetura

### Service Layer Pattern
Cada entidade possui um Service dedicado (SupplierService, ProductService, OrderService, etc.) que encapsula a logica de negocio. Os Controllers ficam "magros", delegando operacoes para os Services. Isso facilita testes e reutilizacao.

### Enums PHP 8.1
`EntityStatus` e `OrderStatus` sao backed enums com metodos utilitarios (`label()`, `canTransitionTo()`, `isTerminal()`, `color()`). Isso centraliza as regras de transicao de estado e elimina strings magicas no codigo.

### Model Events
O `OrderItem` usa model events (`creating`, `updating`, `saved`, `deleted`) para:
- Calcular automaticamente `total_price = quantity * unit_price`
- Recalcular o `total` do pedido pai apos cada operacao

### Soft Deletes
Suppliers, Products e Orders usam SoftDeletes para manter historico e permitir auditoria futura.

## 3. Regras de Negocio

### Maquina de Estado de Pedidos
```
open -> processing | cancelled
processing -> completed | cancelled
completed -> (terminal)
cancelled -> (terminal)
```
Implementada no enum `OrderStatus::canTransitionTo()`. O controller valida a transicao antes de executar.

### Validacao de Itens do Pedido
Um produto so pode ser adicionado a um pedido se:
1. O produto esta **ativo** (`status = active`)
2. O produto esta **vinculado ao fornecedor** do pedido (tabela pivot `product_supplier`)
3. O pedido **nao esta em estado terminal** (completed/cancelled)

### Bloqueio de Fornecedor Inativo
Fornecedores com `status = inactive` nao podem receber novos pedidos. Validado no `OrderController::store()`.

### Historico de Status
Cada transicao de status gera um registro em `order_status_histories` com `old_status`, `new_status` e `changed_at`. Exibido como timeline visual na pagina do pedido.

## 4. Operacoes Bulk (Jobs)

### BulkLinkProductSuppliers / BulkUnlinkProductSuppliers
Jobs assincronos processados via Redis que vinculam/desvinculam multiplos produtos a fornecedores. Caracteristicas:
- **Idempotente**: `syncWithoutDetaching()` garante que vinculos duplicados nao sao criados
- **3 tentativas**: Configurado via `$tries = 3`
- **Feedback ao usuario**: Flash message informando que a operacao foi enviada para processamento

## 5. Frontend

### Composable `useFilters`
Abstrai a logica de filtros sincronizados com a URL via Inertia router. Permite busca, ordenacao e filtragem por status em qualquer listagem.

### Componentes Reutilizaveis
- `DataTable`: Tabela com headers clicaveis para ordenacao
- `Pagination`: Links de paginacao estilizados
- `SearchInput`: Input com debounce de 300ms
- `StatusBadge`: Badge colorido baseado no status
- `ConfirmModal`: Modal de confirmacao com variantes danger/primary
- `CnpjInput/PhoneInput`: Inputs com mascara automatica
- `CurrencyInput`: Input monetario formatado em BRL

### Layout Sidebar
Layout com sidebar fixa (desktop) e colapsavel (mobile), substituindo o header navigation padrao do Breeze. Inclui:
- Navegacao com icones SVG
- Indicador visual de rota ativa
- Area de usuario com botao de logout
- Flash messages globais (success/error)

## 6. Decisoes de UX

### Dual Panel (Vinculos)
A pagina de vinculos usa dois paineis lado a lado: "Produtos Vinculados" e "Produtos Disponiveis". Isso permite visualizar rapidamente o estado atual e fazer operacoes individuais ou em massa (bulk).

### Pedido Show Page
A pagina de detalhe do pedido concentra todas as operacoes:
- Adicionar/remover itens (inline)
- Transicionar status (botoes no header)
- Timeline de historico (sidebar)
- Totais calculados automaticamente

### Filtros Avancados em Pedidos
A listagem de pedidos suporta filtros combinados: busca textual, filtro por status e filtro por fornecedor, todos sincronizados com a URL.
