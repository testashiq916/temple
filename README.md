# Temple ERP

A multi-tenant SaaS platform for temple management: devotee records, seva
(ritual) bookings, donations & e-hundi, double-entry accounting, crowd/queue
management, property, inventory, and staff — built from design notes
originally exported from a DeepSeek chat session (see `extracted/temple/`).

```
temple-erp/
├── backend/laravel/     Laravel 12 API (migrations, models, controllers, ledger service)
├── frontend/web-admin/  React + TypeScript admin console (Vite)
├── frontend/mobile-app/ Flutter devotee portal (source only — see its README)
├── docker/              docker-compose stack: MySQL + API + web-admin
├── scripts/             setup.sh / reset-db.sh dev helpers
└── docs/API.md          API endpoint reference
```

## Quick start (local, no Docker)

```bash
./scripts/setup.sh
```

This installs backend + web-admin dependencies, generates an app key, sets
up a SQLite database, and runs migrations + seeders. Then, in two terminals:

```bash
cd backend/laravel && php artisan serve        # http://localhost:8000
cd frontend/web-admin && npm run dev            # http://localhost:5173
```

Sign in with the seeded demo admin: `admin@temple.test` / `password`
(devotee login: `devotee@temple.test` / `password`).

## Quick start (Docker)

```bash
docker compose -f docker/docker-compose.yml up --build
```

Brings up MySQL, the Laravel API (`:8000`), and the web-admin console
(`:5173`). Run migrations/seeders once the containers are healthy:

```bash
docker compose -f docker/docker-compose.yml exec backend php artisan migrate --seed
```

## What's implemented

- **Backend** (`backend/laravel`): 45-table schema across 10 modules,
  matching Eloquent models with relationships, Sanctum-authenticated REST
  API, and a `LedgerService` that posts real double-entry bookkeeping
  entries for donations, seva payments/completion/cancellation, and prasad
  sales. Verified end-to-end (migrate → seed → book a seva → confirm
  payment → donate → trial balance balances).
- **Web admin** (`frontend/web-admin`): login, dashboard, and CRUD pages for
  temples, devotees, seva bookings, donations, and an accounting
  trial-balance view. Verified in-browser against the live API.
- **Mobile app** (`frontend/mobile-app`): Flutter devotee portal source
  (login, seva booking, donations, profile) — authored without a Flutter
  SDK available in this environment, so it hasn't been built or run yet;
  see its README for the one-time `flutter create .` step.

Modules present in the database schema and API but without a dedicated admin
UI yet: crowd/queue management, property, inventory, and staff — all fully
reachable via the API (see `docs/API.md`) and ready for UI pages to be added
the same way the existing ones were.

## Design notes

The original chat-exported design notes (folder tree blueprint, architecture
diagram, and raw SQL schema fragments) live in `extracted/temple/` for
reference.
