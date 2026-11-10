#!/bin/bash
set -e

echo "=== Iniciando GameVault ==="

# Crear carpetas de storage con permisos
echo "Creando carpetas de storage..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache

# Ajustar puerto de Apache antes de migrar
echo "Configurando puerto de Apache..."
export APACHE_RUN_USER=www-data
export APACHE_RUN_GROUP=www-data
export APACHE_LOG_DIR=/var/log/apache2

PORT=${PORT:-10000}
echo "Usando puerto: $PORT"

# Reemplazar cualquier puerto existente con patrones robustos
sed -i "s/Listen [0-9]*/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/VirtualHost \*:[0-9]*/VirtualHost *:$PORT/" /etc/apache2/sites-available/000-default.conf

# Ejecutar migraciones y seeders de forma idempotente
echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Ejecutando seeders..."
php artisan db:seed --force

# Optimizar para producción
echo "Optimizando configuración y vistas..."
php artisan config:cache
php artisan view:cache

# Asegurar permisos después de caché
echo "Ajustando permisos de storage..."
chown -R www-data:www-data storage bootstrap/cache

echo "=== Iniciando Apache en puerto $PORT ==="
apache2-foreground
