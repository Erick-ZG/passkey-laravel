#!/bin/sh
set -e

# Cache de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migraciones automáticas
php artisan migrate --force || true

exec supervisord -n
