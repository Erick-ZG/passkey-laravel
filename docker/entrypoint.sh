#!/usr/bin/env bash
set -e

echo "=== Iniciando Entrypoint Laravel ==="

# 1. Asegurar permisos correctos (importante para logs/sesiones)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Limpiar caches previos (para evitar APP_KEY o DB mal cacheados)
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan event:clear || true

# 3. Enlazar storage (por si no existe el symlink público)
php artisan storage:link || true

# 4. Migraciones (si quieres que se apliquen automáticamente)
# ⚠️ si prefieres control manual, comenta esta línea
php artisan migrate --force || true

# 5. Ahora sí, cachear con las variables de entorno REALES
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Comprobación opcional de la CA (debug útil en Render)
if [ -f "$MYSQL_ATTR_SSL_CA" ]; then
  echo "Certificado CA encontrado en: $MYSQL_ATTR_SSL_CA"
else
  echo "⚠️ Advertencia: No se encontró el archivo CA en $MYSQL_ATTR_SSL_CA"
fi

# 7. Ejecutar la app (Render usa puerto en $PORT)
echo "Servidor Laravel corriendo en puerto $PORT"
exec php artisan serve --host 0.0.0.0 --port $PORT
