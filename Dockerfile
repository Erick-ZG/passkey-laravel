# Imagen base con PHP 8.2 + FPM + Composer
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip zip supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip bcmath \
    && rm -rf /var/lib/apt/lists/*

# Instalar Node.js (para compilar React/Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer (desde imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar directorio de la app
WORKDIR /var/www/html

# Copiar archivos de Laravel
COPY . .

# Instalar dependencias PHP y Node
RUN composer install --no-dev --optimize-autoloader \
    && npm ci && npm run build

# Copiar supervisor config en la ruta correcta
COPY docker/supervisord.conf /etc/supervisord.conf

# Copiar entrypoint y darle permisos
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Render asigna el puerto dinámico en $PORT
ENV PORT=10000
EXPOSE 10000

# Iniciar con entrypoint
ENTRYPOINT ["entrypoint.sh"]
