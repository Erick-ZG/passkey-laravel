#!/usr/bin/env bash
set -e

# En Render, las env vars viven fuera del .env y se leen cuando cacheas config
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# No migramos aquí; lo haremos en preDeploy del Blueprint
exec /usr/bin/supervisord -n -c /opt/docker/etc/supervisord.conf
