# --- Stage 1: build de assets (Vite) ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
# Si usas bun/pnpm, ajusta aquí
RUN npm ci
# Copiamos solo lo necesario para el build
COPY vite.config.* ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# --- Stage 2: dependencias PHP (Composer) ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Copiamos todo para que Composer pueda resolver autoloads
COPY . .
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# --- Stage 3: runtime (Nginx + PHP-FPM en una sola imagen) ---
FROM webdevops/php-nginx:8.3-alpine
ENV WEB_DOCUMENT_ROOT=/app/public
WORKDIR /app

# Extensiones útiles (pgsql por defecto; si usas MySQL, cambia por pdo_mysql)
RUN docker-php-ext-install pdo pdo_pgsql bcmath opcache

# Copiamos el código y artefactos
COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

# Permisos
RUN chown -R application:application /app/storage /app/bootstrap/cache \
 && chmod -R ug+rwX /app/storage /app/bootstrap/cache

# Opcional: forzar HTTPS en producción (ajústalo en AppServiceProvider si prefieres)
# ENV APP_ENV=production

# Entrypoint: cachea config/rutas/vistas y arranca supervisord (nginx + php-fpm)
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER application
CMD ["/entrypoint.sh"]
