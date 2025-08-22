#!/usr/bin/env bash
set -e

# Cachear configuración y rutas
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear el symlink de storage si no existe
php artisan storage:link || true

# Ejecutar la app en el puerto asignado por Render
php artisan serve --host 0.0.0.0 --port $PORT
