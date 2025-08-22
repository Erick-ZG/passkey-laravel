#!/usr/bin/env bash
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# No usamos artisan serve, dejamos que supervisord maneje nginx+php-fpm
exec /usr/bin/supervisord -n -c /opt/docker/etc/supervisord.conf