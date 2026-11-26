#!/bin/bash

set -e

if [ "$IS_LARAVEL" = "true" ]; then
  if [ "$RAILPACK_SKIP_MIGRATIONS" != "true" ]; then
    echo "Running migrations ..."
    php artisan migrate --force
  fi

  echo "Clearing and optimizing Laravel application ..."
  php artisan storage:link
  php artisan optimize:clear
  php artisan optimize

  echo "Optimizing Filament admin panel ..."
  php artisan filament:optimize
fi

# Supervisor runs both FrankenPHP and the queue worker (see supervisord.conf).
echo "Starting Supervisor (FrankenPHP + queue worker) ..."
exec /usr/bin/supervisord -c /app/supervisord.conf
