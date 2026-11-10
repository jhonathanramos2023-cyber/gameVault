# Despliegue en Render

Esta guía explica cómo desplegar GameVault en Render como aplicación web con Docker, PostgreSQL y almacenamiento S3-compatible (Cloudflare R2 o Supabase).

## Requisitos previos

- Cuenta en Render (https://render.com)
- Repositorio de GitHub con el código de GameVault
- Base de datos PostgreSQL en Render
- Bucket S3-compatible (Cloudflare R2 o Supabase Storage)

## 1. Crear servicios en Render

### 1.1 Base de datos PostgreSQL

1. En Render, crea un nuevo "PostgreSQL"
2. Nombre: `gamevault-db`
3. Región: elige la más cercana a tus usuarios
4. Plan: Free (para desarrollo) o paid para producción
5. Copia la "Internal Database URL" (la necesitarás más tarde)

### 1.2 Bucket S3-compatible (Cloudflare R2)

Para Cloudflare R2:
1. Crea un bucket en Cloudflare R2
2. Configura un dominio público (ej. `gamevault.r2.dev` o tu propio dominio)
3. Genera API Token con acceso al bucket
4. Para `AWS_URL`, usa el dominio público del bucket (ej. `https://gamevault.r2.dev`)
5. `AWS_USE_PATH_STYLE_ENDPOINT` debe ser `true` para R2

Para Supabase Storage:
1. Crea un proyecto en Supabase
2. Habilita Storage
3. Genera API keys
4. `AWS_URL` es la URL del proyecto (ej. `https://xyz.supabase.co`)
5. `AWS_USE_PATH_STYLE_ENDPOINT` suele ser `false`

### 1.3 Aplicación web (Docker)

1. En Render, crea un nuevo "Web Service"
2. Conecta tu repositorio de GitHub
3. Configuración:
   - **Name**: `gamevault`
   - **Region**: misma que la base de datos
   - **Runtime**: Docker
   - **Branch**: `master` o `main`
   - **Root Directory**: (dejar vacío)
   - **Docker Context**: `/`
   - **Dockerfile Path**: `Dockerfile`

## 2. Variables de entorno

Configura estas variables de entorno en Render (sin valores reales en el código):

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `APP_ENV` | Entorno | `production` |
| `APP_KEY` | Clave de encriptación de Laravel (generar con `php artisan key:generate --show`) | `base64:...` |
| `APP_DEBUG` | Modo debug | `false` |
| `APP_URL` | URL pública de la app | `https://gamevault.onrender.com` |
| `APP_LOCALE` | Idioma | `es` |
| `DB_CONNECTION` | Tipo de base de datos | `pgsql` |
| `DB_URL` | URL de conexión PostgreSQL (la copiaste de Render) | `postgresql://...` |
| `SESSION_DRIVER` | Driver de sesión | `database` |
| `CACHE_STORE` | Driver de caché | `database` |
| `QUEUE_CONNECTION` | Driver de cola | `database` |
| `FILESYSTEM_DISK` | Disco de archivos por defecto | `local` |
| `GAMEVAULT_IMAGE_DISK` | Disco para imágenes | `s3` |
| `AWS_ACCESS_KEY_ID` | Clave de acceso AWS/S3 | `AKIA...` |
| `AWS_SECRET_ACCESS_KEY` | Secreto de acceso AWS/S3 | `secret...` |
| `AWS_DEFAULT_REGION` | Región AWS | `us-east-1` |
| `AWS_BUCKET` | Nombre del bucket | `gamevault-images` |
| `AWS_ENDPOINT` | Endpoint personalizado (R2/Supabase) | `https://...r2.dev` |
| `AWS_URL` | URL pública del bucket | `https://gamevault.r2.dev` |
| `AWS_USE_PATH_STYLE_ENDPOINT` | Estilo de path endpoint | `true` (R2) / `false` (Supabase) |
| `GAMEVAULT_SEED_DEMO` | Crear datos de ejemplo | `false` |
| `ADMIN_EMAIL` | Email del administrador | `admin@tudominio.com` |
| `ADMIN_PASSWORD` | Contraseña del administrador (secreto) | `contraseña_segura` |

### Generar APP_KEY

En tu máquina local:
```bash
php artisan key:generate --show
```

Copia el resultado y pégalo como valor de `APP_KEY` en Render.

## 3. Configurar health check

Render usa automáticamente `/up` como health check (configurado en `bootstrap/app.php`). No requiere configuración adicional.

## 4. Primer despliegue

1. Haz push de tus cambios a GitHub
2. Render detectará el nuevo commit y comenzará el build
3. El build:
   - Ejecuta `npm ci` y `npm run build` (etapa Node)
   - Ejecuta `composer install --no-dev` (etapa PHP)
   - Ejecuta `php artisan migrate --force` (migraciones)
   - Ejecuta `php artisan db:seed --force` (seeders)
4. Al terminar, la app estará disponible en `https://gamevault.onrender.com`

## 5. Verificación

1. Accede a la URL de tu app
2. Inicia sesión con `ADMIN_EMAIL` y `ADMIN_PASSWORD`
3. Verifica que:
   - El panel de administración funciona
   - Puedes crear categorías y plataformas
   - Puedes crear colaboradores
   - Las imágenes se suben al bucket S3-compatible
   - Las imágenes se muestran con la URL pública del bucket

## 6. Mantenimiento

### Actualizar la aplicación

1. Haz cambios en el código
2. Push a GitHub
3. Render detectará el cambio y redeployará automáticamente

### Limpiar datos de ejemplo

En producción, `GAMEVAULT_SEED_DEMO=false` asegura que solo se cree el administrador, categorías y plataformas base. Los colaboradores y videojuegos de ejemplo NO se crean.

### Backups

Render hace backups automáticos de PostgreSQL. Para el bucket S3-compatible, configura retención según tu proveedor.
