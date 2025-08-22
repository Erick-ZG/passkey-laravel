#!/usr/bin/env bash
set -e

# Preparar Laravel (sin correr migraciones aquí)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# Render pone el puerto en $PORT
echo "Starting supervisord on port $PORT..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf