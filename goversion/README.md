# Iceburg CRM Go Version

This folder contains a standalone Go port of the Iceburg CRM runtime. It serves the compiled Vue/Inertia frontend assets from `public/` and implements the CRM backend routes, authentication, metadata-driven module CRUD, admin builder APIs, datalets, settings, connectors, relationships, and dynamic module table generation in Go.

The original Laravel source artifacts are also mirrored here for parity and future rebuilds:

- `app/`, `routes/`, `config/`, `database/`, `tests/`, `composer.json`, `composer.lock`, and `phpunit.xml` are the Laravel backend/schema/test reference.
- `resources/`, `package.json`, `package-lock.json`, `webpack.mix.js`, `tailwind.config.js`, and `jsconfig.json` are the Vue/Inertia frontend source and build reference.
- `public/` contains the compiled assets served by the Go runtime.

The executable port is the Go code under `cmd/` and `internal/`; the mirrored Laravel files are retained so the Go version is not missing any original frontend/backend source context.

## Run

```bash
cp .env.example .env
go mod download
go run ./cmd/iceburg-go migrate
go run ./cmd/iceburg-go serve
```

By default the server listens on `:8090` and connects to the same MySQL database settings used by the Laravel docker compose file.

## Commands

```bash
go run ./cmd/iceburg-go serve
go run ./cmd/iceburg-go migrate
go run ./cmd/iceburg-go seed
go run ./cmd/iceburg-go generate-modules
```

The default seeded login is `admin@example.com` / `password`.
