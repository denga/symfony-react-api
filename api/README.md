# API

Symfony-based REST API for creating orders. Tech stack: PHP/Symfony, Doctrine ORM, PostgreSQL, Docker.

## Architecture

The project follows a **layered architecture** (inspired by DDD/Hexagonal):

**Architecture layers (top-down, dependencies flow inward):**

```mermaid
flowchart TB
    subgraph controller [Controller]
        OrderCtrl[OrderController]
    end
    subgraph ui [UI Layer]
        ReqRes[Request/Response DTOs, Mappers, Validation]
    end
    subgraph app [Application Layer]
        Handlers[Commands, Handlers, Use Cases]
    end
    subgraph domain [Domain Layer]
        Models[Models, Factories, Repository Interfaces]
    end
    subgraph infra [Infrastructure Layer]
        Doctrine[Doctrine, Persistence, Mappers]
    end
    OrderCtrl --> ReqRes
    ReqRes --> Handlers
    Handlers --> Models
    Models --> Doctrine
```

**Request flow (Create Order):**

```mermaid
flowchart TB
    subgraph UI [UI Layer]
        Controller[OrderController]
        Request[CreateOrderRequest]
        Response[CreateOrderResponse]
        RequestMapper[RequestToCommandMapper]
    end
    
    subgraph Application [Application Layer]
        Handler[CreateOrderHandler]
        Command[CreateOrderCommand]
        Result[CreateOrderResult]
    end
    
    subgraph Domain [Domain Layer]
        OrderFactory[OrderFactory]
        Order[Order Model]
        OrderRepo[OrderRepositoryInterface]
    end
    
    subgraph Infrastructure [Infrastructure Layer]
        DoctrineRepo[DoctrineOrderRepository]
        OrderEntity[OrderDoctrineEntity]
        OrderMapper[OrderMapper]
    end
    
    Controller --> RequestMapper
    RequestMapper --> Handler
    Handler --> OrderFactory
    OrderFactory --> Order
    Handler --> OrderRepo
    OrderRepo --> DoctrineRepo
    DoctrineRepo --> OrderMapper
    OrderMapper --> OrderEntity
```

**Request flow (List Orders):**

```mermaid
flowchart TB
    subgraph UI [UI Layer]
        ListController[OrderController]
        ListResponse[OrdersListResponse]
    end
    
    subgraph Application [Application Layer]
        ListHandler[ListOrdersHandler]
        ListQuery[ListOrdersQuery]
        OrderSummary[OrderSummary]
    end
    
    subgraph Domain [Domain Layer]
        OrderRepo[OrderRepositoryInterface]
        Order[Order Model]
    end
    
    subgraph Infrastructure [Infrastructure Layer]
        DoctrineRepo[DoctrineOrderRepository]
    end
    
    ListController --> ListQuery
    ListController --> ListHandler
    ListHandler --> ListQuery
    ListHandler --> OrderRepo
    OrderRepo --> DoctrineRepo
    DoctrineRepo --> Order
    ListHandler --> OrderSummary
    OrderSummary --> ListResponse
```

**Layers overview:**

| Layer              | Path                  | Responsibility                                        |
| ------------------ | --------------------- | ----------------------------------------------------- |
| **Controller**     | `src/Controller/`     | HTTP routing, delegation to Application               |
| **Commands**       | `src/Command/`        | CLI commands, delegation to Application               |
| **UI**             | `src/UI/Api/`         | Request/Response DTOs, validation, exception handling |
| **Application**    | `src/Application/`    | Use cases (Commands/Handlers), transaction control   |
| **Domain**         | `src/Domain/`         | Business logic, models, repository interfaces        |
| **Infrastructure** | `src/Infrastructure/` | Doctrine entities, persistence, mappers               |

## API Endpoints

| Method | Path | Description |
|--------|------|-------------|
| **POST** | `/api/orders` | Create order. Body: `customerId`, `items` (array with `sku`, `quantity`, `price_cents`). Response: `orderId`, `orderUrl` (201 Created) |
| **GET** | `/api/orders` | List orders (paginated). Query: `page` (default 1), `perPage` (default 20). Response: `meta` (total, page, perPage, totalPages), `data` (items) |

## Testing with curl

**Create order (POST):**

```bash
curl -k -X POST https://localhost:443/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customerId": "cust-123",
    "items": [
      {"sku": "PROD-001", "quantity": 2, "price_cents": 1999},
      {"sku": "PROD-002", "quantity": 1, "price_cents": 4999}
    ]
  }'
```

**List orders (GET):**

```bash
curl -k -X GET "https://localhost:443/api/orders?page=1&perPage=20"
```

- `-k` for self-signed SSL certificates (Docker/Caddy)
- Port 443 per `compose.yaml`; use 80 for HTTP if needed

## Console Commands

**app:create-order** – Create order from CLI

- Argument: `customerId` (required)
- Option: `--item "sku:quantity:price_cents"` (repeatable)

```bash
# Local
php bin/console app:create-order cust-123 --item "sku-1:2:1999" --item "sku-2:1:499"

# Docker (service name: php)
docker compose exec php php bin/console app:create-order cust-123 --item "sku-1:2:1999" --item "sku-2:1:499"
```

## Project Structure

- `src/Controller` – HTTP controllers
- `src/Command` – Console commands (CLI entry points)
- `src/UI/Api` – Request/Response DTOs, mappers, validation
- `src/Application` – Commands, handlers, use cases
- `src/Domain` – Domain models, factories, repository interfaces
- `src/Infrastructure` – Doctrine entities, persistence, mappers

## Development Tools

Run from the project root (api directory):

| Tool | Command | Description |
|------|---------|-------------|
| **PHPStan** | `./vendor/bin/phpstan analyse` | Static analysis |
| **Rector** | `./vendor/bin/rector process` | Automated refactoring |
| **ECS** | `./vendor/bin/ecs check` | Code style check |
| **ECS** | `./vendor/bin/ecs fix` | Apply code style fixes |
| **Deptrac** | `./vendor/bin/deptrac analyse` | Architecture dependency analysis |
