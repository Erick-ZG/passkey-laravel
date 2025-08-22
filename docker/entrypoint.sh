#!/usr/bin/env bash
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# Inicia PHP-FPM en foreground (Render expondrá el puerto)
exec php-fpm