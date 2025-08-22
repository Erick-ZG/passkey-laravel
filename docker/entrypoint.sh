#!/usr/bin/env bash
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# No migramos aquí; eso se hace en el preDeploy de Render
exec /usr/bin/supervisord -n -c /etc/supervisord.conf