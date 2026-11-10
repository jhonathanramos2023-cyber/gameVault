# Etapa 1: Node 22 para construir assets
FROM node:22 AS node-build

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# Etapa 2: PHP 8.3 con Apache para producción
FROM php:8.3-apache

# Instalar librerías necesarias para extensiones PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configurar GD con freetype y jpeg
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo_pgsql pdo_mysql gd zip mbstring exif bcmath opcache

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Configurar PHP para producción
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "opcache.enable=1" >> "$PHP_INI_DIR/php.ini" \
    && echo "opcache.memory_consumption=128" >> "$PHP_INI_DIR/php.ini" \
    && echo "opcache.interned_strings_buffer=8" >> "$PHP_INI_DIR/php.ini" \
    && echo "opcache.max_accelerated_files=10000" >> "$PHP_INI_DIR/php.ini" \
    && echo "upload_max_filesize=5M" >> "$PHP_INI_DIR/php.ini" \
    && echo "post_max_size=8M" >> "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit=256M" >> "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Copiar composer files e instalar dependencias
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copiar código de la aplicación
COPY . .

# Copiar assets construidos desde la etapa Node
COPY --from=node-build /app/public/build ./public/build

# Optimizar autoloader y descubrir paquetes después de copiar todo el código
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi

# Configurar DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/sites-available/000-default.conf

# Copiar script de inicio y eliminar \r por seguridad
COPY docker/start.sh /usr/local/bin/start.sh
RUN sed -i 's/\r$//' /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Crear carpetas de storage
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV PORT=10000
EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
