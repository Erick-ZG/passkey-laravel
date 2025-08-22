# --- Stage 1: Build assets (Vite/React) ---
FROM node:20-alpine AS assets
WORKDIR /app

# Copiamos solo lo necesario para instalar dependencias
COPY package*.json ./
RUN npm ci

COPY vite.config.* ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# --- Stage 2: Dependencias PHP (Composer) ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
COPY . .
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# --- Stage 3: Runtime (PHP-FPM + Nginx) ---
FROM webdevops/php-nginx:8.3-alpine

# Document root
ENV WEB_DOCUMENT_ROOT=/app/public
WORKDIR /app

# Extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql bcmath opcache

# Copiamos el código y assets
COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

# Permisos
RUN chown -R application:application /app/storage /app/bootstrap/cache \
    && chmod -R ug+rwX /app/storage /app/bootstrap/cache

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER application
CMD ["/entrypoint.sh"]
