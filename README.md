# Symfony React API

Full-stack monorepo with a **Symfony API** (FrankenPHP), a **React frontend** (TanStack Start / Vite), and a complete **observability stack** (OpenTelemetry, Grafana, Prometheus, Tempo, Loki, Jaeger).

## Architecture

```
                    ┌────────────────┐
                    │   Frontend     │
                    │  (TanStack     │
                    │   Start SSR)   │
                    └───────┬────────┘
                            │ /api
                    ┌───────▼────────┐
                    │      API       │
                    │  (Symfony /    │
                    │  FrankenPHP)   │
                    └───────┬────────┘
                            │
                    ┌───────▼────────┐
                    │   PostgreSQL   │
                    └────────────────┘

  Traces & logs ──► OTEL Collector ──► Tempo / Loki / Prometheus ──► Grafana
                                   └──► Jaeger
```

| Component | Technology                                                        |
|-----------|-------------------------------------------------------------------|
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

   Review `.env` and adjust values as needed (database credentials, ports, secrets).

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

### Service URLs (Development)

| Service | URL |
|---------|-----|
| Frontend | [http://localhost:3001](http://localhost:3001) |
| API (HTTPS) | [https://localhost](https://localhost) |
| API (HTTP) | [http://localhost](http://localhost) |
| Grafana | [http://localhost:3000](http://localhost:3000) |
| Prometheus | [http://localhost:9090](http://localhost:9090) |
| Jaeger UI | [http://localhost:16686](http://localhost:16686) |
| Tempo | [http://localhost:3200](http://localhost:3200) |
| Loki | [http://localhost:3100](http://localhost:3100) |

## Production

Build and run with the production overrides:

```bash
docker compose -f compose.yaml -f compose.prod.yaml up -d --build
```

Make sure the following variables are set in `.env` for production:

- `APP_SECRET` -- Symfony application secret
- `CADDY_MERCURE_JWT_SECRET` -- secure JWT key for Mercure
- `POSTGRES_PASSWORD` -- strong database password
- `SERVER_NAME` -- your domain name

## Running Individual Services

```bash
# API + database only
docker compose up php database

# Observability stack only
docker compose up tempo loki prometheus grafana otel-collector jaeger

# Frontend only
docker compose up app
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
├── docker/                     # Observability infrastructure config
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
