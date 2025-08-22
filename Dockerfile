# --- Base PHP 8.2 FPM ---
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip bcmath

# Instalar Node.js (para Vite/React)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && node -v && npm -v

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de Laravel
COPY . .

# Instalar dependencias PHP y Node
RUN composer install --no-dev --optimize-autoloader \
    && npm ci && npm run build

# Ajustar permisos para que Render no necesite root
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto dinámico asignado por Render
ENV PORT=10000
EXPOSE 10000

# Entrypoint simplificado
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

#bundle
COPY storage/certs/singlestore_bundle.pem /var/www/html/storage/certs/singlestore_bundle.pem

ENTRYPOINT ["entrypoint.sh"]