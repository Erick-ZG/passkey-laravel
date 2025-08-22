#!/usr/bin/env bash
set -e

echo "=== Iniciando Entrypoint Laravel ==="

# 1. Permisos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Limpiar caches previos
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan event:clear || true

# 3. Storage link
php artisan storage:link || true

# 4. Migraciones (solo si quieres automáticas)
# php artisan migrate --force || true

# 5. Cachear con variables de entorno reales
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Validar certificado
if [ -f "/var/www/html/$MYSQL_ATTR_SSL_CA" ]; then
  echo "Certificado CA encontrado en: /var/www/html/$MYSQL_ATTR_SSL_CA"
else
  echo "⚠️ Advertencia: No se encontró el archivo CA en /var/www/html/$MYSQL_ATTR_SSL_CA"
fi

# 7. Levantar servidor
echo "Servidor Laravel corriendo en puerto $PORT"
exec php artisan serve --host 0.0.0.0 --port $PORT
