#!/bin/bash

set -e

if [ "$IS_LARAVEL" = "true" ]; then
  if [ "$RAILPACK_SKIP_MIGRATIONS" != "true" ]; then
    # Run migrations and seeding
    echo "Running migrations and seeding database ..."
    php artisan migrate --force
  fi

  echo "Clearing and optimizing Laravel application ..."
  php artisan storage:link
  php artisan optimize:clear
  php artisan optimize

  echo "Optimizing Filament admin panel ..."
  php artisan filament:optimize

#   echo "Starting Laravel queue worker ..."
#   php artisan queue:work \
#     --sleep=3 \
#     --tries=3 \
#     --timeout=60 &

#   echo "Starting Laravel server ..."
fi

# Start the FrankenPHP server
# docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1

echo "Starting Supervisor (managing FrankenPHP & Queue Worker)..."
exec /usr/bin/supervisord -c /app/supervisord.conf
