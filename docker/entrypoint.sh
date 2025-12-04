#!/bin/sh
set -e

if [ ! -f /app/database/database.sqlite ]; then
  mkdir -p /app/database
  touch /app/database/database.sqlite
  chmod 664 /app/database/database.sqlite || true
fi

mkdir -p \
  /app/storage/logs \
  /app/storage/framework/cache \
  /app/storage/framework/sessions \
  /app/storage/framework/views \
  /app/bootstrap/cache
chmod -R 777 /app/storage /app/bootstrap/cache || true

if [ ! -d /app/vendor ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader || true
fi

php artisan key:generate --force || true

php artisan optimize:clear || true

if [ "$ALWAYS_FRESH" = "true" ]; then
  echo "[entrypoint] Running migrate:fresh --seed (ALWAYS_FRESH=true)"
  php artisan migrate:fresh --force
  php artisan db:seed --force
else
  echo "[entrypoint] Running migrate --seed"
  php artisan migrate --force
  php artisan db:seed --force
fi

exec php artisan serve --host=0.0.0.0 --port=8080
