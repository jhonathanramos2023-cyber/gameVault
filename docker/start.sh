#!/bin/bash
set -e

# Crear carpetas de storage con permisos
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache

# Ejecutar migraciones y seeders de forma idempotente
php artisan migrate --force
php artisan db:seed --force

# Iniciar Apache en el puerto especificado por Render
export APACHE_RUN_USER=www-data
export APACHE_RUN_GROUP=www-data
export APACHE_LOG_DIR=/var/log/apache2

# Render usa la variable PORT, Apache escucha en 8080 por defecto
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
    sed -i "s/VirtualHost \*:80/VirtualHost *:$PORT/" /etc/apache2/sites-available/000-default.conf
fi

apache2-foreground
