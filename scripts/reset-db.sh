#!/usr/bin/env bash
# Drops and recreates all tables, then reseeds demo data. Destructive —
# only use in local/dev environments.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR/backend/laravel"

php artisan migrate:fresh --seed --no-interaction
echo "Database reset and reseeded."
