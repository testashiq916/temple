#!/usr/bin/env bash
# One-shot local dev setup: installs backend + web-admin dependencies,
# prepares a SQLite database, and runs migrations + seeders.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "==> Installing Laravel backend dependencies"
cd "$ROOT_DIR/backend/laravel"
composer install
[ -f .env ] || cp .env.example .env
php artisan key:generate --no-interaction

if grep -q '^DB_CONNECTION=sqlite' .env; then
  mkdir -p database
  touch database/database.sqlite
fi

php artisan migrate --seed --no-interaction

echo "==> Installing web-admin dependencies"
cd "$ROOT_DIR/frontend/web-admin"
npm install
[ -f .env.local ] || cp .env.example .env.local

echo "==> Done. Next steps:"
echo "  Backend:   cd backend/laravel && php artisan serve"
echo "  Web admin: cd frontend/web-admin && npm run dev"
echo "  Demo login: admin@temple.test / password"
