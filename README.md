# Symfony React API

Full-stack monorepo with a **Symfony API** (FrankenPHP), a **React frontend** (TanStack Start / Vite), and a complete **observability stack** (OpenTelemetry, Grafana, Prometheus, Tempo, Loki, Jaeger). All services are accessible through a single **Caddy reverse proxy**.

## Architecture

```
                         ┌──────────────────┐
                         │  Caddy Reverse   │
              Browser ──►│  Proxy :80/:443  │
                         └────────┬─────────┘
                                  │
                 ┌────────────────┼────────────────┐
                 │                │                 │
          /api/* │          /     │  *.local.gd      │
                 ▼                ▼                  ▼
         ┌──────────────┐ ┌──────────────┐  ┌──────────────┐
         │     API      │ │   Frontend   │  │ Observability│
         │  (Symfony /  │ │  (TanStack   │  │  (Grafana,   │
         │  FrankenPHP) │ │  Start SSR)  │  │  Jaeger ...) │
         └──────┬───────┘ └──────────────┘  └──────────────┘
                │
         ┌──────▼───────┐
         │  PostgreSQL  │
         └──────────────┘

  Traces & logs ──► OTEL Collector ──► Tempo / Loki / Prometheus ──► Grafana
                                   └──► Jaeger
```

| Component | Technology                                                        |
|-----------|-------------------------------------------------------------------|
| Reverse Proxy | Caddy 2                                                           |
| API | Symfony 8 / PHP 8.4 / FrankenPHP                                  |
| Frontend | React 19 / TanStack Start / Vite 7 / Tailwind 4                   |
| Database | PostgreSQL 16                                                     |
| Observability | OpenTelemetry Collector, Grafana, Prometheus, Tempo, Loki, Jaeger |

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) (v24+)
- [Docker Compose](https://docs.docker.com/compose/install/) (v2.20+)

## Getting Started

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd symfony-react-api
   ```

2. **Configure environment variables**

   ```bash
   cp .env.example .env
   ```

   Review `.env` and adjust values as needed (database credentials, secrets).

3. **Start the development environment**

   ```bash
   docker compose up --build
   ```

   This automatically loads `compose.override.yaml` with dev-specific settings (hot reload, Xdebug, exposed database port).

## Development

The dev environment mounts source code into the containers for live reloading:

- **API**: PHP files are synced into the FrankenPHP container with file watching enabled.
- **Frontend**: Vite dev server with HMR runs inside the container, source files are bind-mounted.
- **Database**: Port is exposed to the host for use with local database tools.

All services are accessed through the Caddy reverse proxy. The domain `local.gd` (and all its subdomains) resolves to `127.0.0.1`, so no `/etc/hosts` changes are needed. Frontend and API share `app.local.gd` (same origin, path-based routing); observability tools get their own subdomains.

### Service URLs

| Service | URL |
|---------|-----|
| Frontend | [https://app.local.gd](https://app.local.gd) |
| API | [https://app.local.gd/api/...](https://app.local.gd/api/) |
| API Docs (Swagger) | [https://app.local.gd/api/doc](https://app.local.gd/api/doc) |
| Grafana | [https://grafana.local.gd](https://grafana.local.gd) |
| Jaeger | [https://jaeger.local.gd](https://jaeger.local.gd) |
| Prometheus | [https://prometheus.local.gd](https://prometheus.local.gd) |

## Production

Build and run with the production overrides:

```bash
docker compose -f compose.yaml -f compose.prod.yaml up -d --build
```

Make sure the following variables are set in `.env` for production:

- `APP_SECRET` -- Symfony application secret
- `CADDY_MERCURE_JWT_SECRET` -- secure JWT key for Mercure
- `POSTGRES_PASSWORD` -- strong database password
- `PROXY_DOMAIN` -- your domain name (e.g. `example.com`)

## Running Individual Services

```bash
# API + database only
docker compose up proxy php database

# Observability stack only
docker compose up proxy tempo loki prometheus grafana otel-collector jaeger

# Frontend only
docker compose up proxy app php database
```

## Project Structure

```
.
├── api/                        # Symfony API application
│   ├── Dockerfile              # Multi-stage build (dev / prod)
│   ├── config/                 # Symfony configuration
│   ├── frankenphp/             # FrankenPHP config (Caddyfile, PHP INI, entrypoint)
│   ├── migrations/             # Doctrine migrations
│   ├── src/                    # PHP source code
│   └── tests/                  # API tests
├── app/                        # React frontend application
│   ├── Dockerfile              # Multi-stage build (dev / prod)
│   ├── src/                    # TypeScript / React source code
│   │   ├── components/         # UI components (shadcn/ui)
│   │   ├── routes/             # TanStack Router file-based routes
│   │   └── api/                # API client layer
│   └── public/                 # Static assets
├── docker/                     # Infrastructure config
│   ├── caddy/                  # Reverse proxy Caddyfile
│   ├── grafana/                # Grafana datasource provisioning
│   ├── otel-collector/         # OpenTelemetry Collector config
│   ├── prometheus/             # Prometheus scrape config
│   └── tempo/                  # Tempo config
├── compose.yaml                # Base compose (all services)
├── compose.override.yaml       # Development overrides (auto-loaded)
├── compose.prod.yaml           # Production overrides
├── .env                        # Environment variables
└── .env.example                # Environment variable template
```

## Compose Files

| File | Purpose | Loaded |
|------|---------|--------|
| `compose.yaml` | Base service definitions for all components | Always |
| `compose.override.yaml` | Dev overrides (build targets, volume mounts, debug tools) | Automatically with `docker compose up` |
| `compose.prod.yaml` | Prod overrides (optimized builds, secrets, restart policies) | Explicitly via `-f compose.yaml -f compose.prod.yaml` |
