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

git config --global --add safe.directory /app >/dev/null 2>&1 || true

if [ ! -f /app/vendor/autoload.php ] || [ ! -d /app/vendor/php-amqplib/php-amqplib ]; then
  composer clear-cache || true
  composer install --no-dev --no-interaction --prefer-source --optimize-autoloader || true
fi

if [ "$#" -gt 0 ]; then
  exec "$@"
fi

if [ -f /app/.env ] && grep -q '^APP_KEY=$' /app/.env; then
  php artisan key:generate --force || true
fi

php artisan optimize:clear || true

if [ "$ALWAYS_FRESH" = "true" ]; then
  echo "[entrypoint] Running migrate:fresh --seed (ALWAYS_FRESH=true)"
  php artisan migrate:fresh --force
  php artisan db:seed --force
else
  echo "[entrypoint] Running migrate"
  php artisan migrate --force
  if [ "$SEED_ON_BOOT" = "true" ]; then
    echo "[entrypoint] Running db:seed (SEED_ON_BOOT=true)"
    php artisan db:seed --force
  fi
fi

cd public
exec php -S 0.0.0.0:8080 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
