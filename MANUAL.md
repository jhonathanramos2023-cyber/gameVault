# Manual de instalación y uso de GameVault

## 1. Preparar el entorno

Instala PHP 8.3 o superior con las extensiones `pdo_pgsql`, `pdo_mysql`, `pdo_sqlite`, `fileinfo`, `gd`, `mbstring`, `openssl`, `xml`, `tokenizer` y `zip`; también Composer, Node.js y npm.

Desde la carpeta de la aplicación:

```bash
cd artifacts/gamevault
composer install
cp .env.example .env
php artisan key:generate
```

Define `APP_NAME=GameVault`, `APP_LOCALE=es` y `APP_FALLBACK_LOCALE=es` en `.env`.

### PostgreSQL

`DB_CONNECTION=pgsql`. Replit proporciona `DATABASE_URL`; en otra instalación puedes usar `DB_URL` o completar los campos `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

### MySQL / MariaDB

Usa `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

### SQLite

Usa `DB_CONNECTION=sqlite`, establece `DB_DATABASE=database/database.sqlite` y crea el archivo vacío si no existe:

```bash
touch database/database.sqlite
```

Completa la instalación:

```bash
npm ci
npm run build
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

El servidor de desarrollo debe escuchar en `0.0.0.0` y usar el puerto asignado por el entorno. En Replit la vista previa inyecta `PORT`.

## 2. Cuentas de prueba locales

Después de sembrar la base de datos:

- Administrador: `admin@gamevault.local` / `GameVaultAdmin2026!`
- Colaborador: `colaborador1@gamevault.local` / `GameVaultLocal2026!`
- Colaborador: `colaborador2@gamevault.local` / `GameVaultLocal2026!`

Son credenciales de desarrollo local. No las publiques ni las uses en producción. El seeder de producción requiere que `ADMIN_PASSWORD` exista en el entorno y no asigna una contraseña fija.

## 3. Uso por rol

### Visitante

1. Abre el catálogo para consultar únicamente fichas publicadas.
2. Busca por título o palabras clave y filtra por categoría, plataforma, formato o tipo de proyecto.
3. Abre una ficha para leer su descripción, clasificación, advertencia, imágenes y fuentes externas.

GameVault no proporciona descargas ni evalúa los destinos externos. La consulta de una referencia no confirma la legalidad, seguridad ni disponibilidad de su contenido.

### Colaborador

1. Inicia sesión con la cuenta creada por administración.
2. Crea una ficha con descripción, categoría, plataformas y clasificación; carga al menos una imagen editorial como portada.
3. Agrega o actualiza referencias documentales desde la ficha.
4. Guarda el borrador y usa **Enviar a revisión** cuando esté listo.
5. Consulta el estado en el panel. Solo puede editar sus propios borradores; no puede publicar, administrar usuarios ni ver registros ajenos.

Las imágenes aceptadas son JPG, PNG o WEBP, hasta 2 MB cada una. La vista previa aparece antes de cargar. El texto alternativo se puede editar junto con cada selección.

### Administrador

1. Consulta el resumen de registros, borradores, publicaciones y usuarios activos.
2. Revisa fichas en estado **En revisión** y publica o devuelve a borrador.
3. Despublica una ficha para volverla a borrador, o retírala del catálogo.
4. Administra categorías, plataformas y cuentas de colaborador.
5. Puede editar cualquier ficha y eliminarla con confirmación; al eliminarla también se borran sus imágenes almacenadas y referencias.

Las categorías y plataformas que tengan fichas asociadas no se pueden eliminar. Las cuentas se activan o desactivan desde la administración; no hay registro público ni eliminación de la propia cuenta.

## 4. Imágenes y almacenamiento

`GAMEVAULT_IMAGE_DISK=local` es el valor predeterminado y guarda imágenes en `storage/app/public`. `php artisan storage:link` las publica bajo `/storage`. Para S3, configura las variables estándar `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` y opcionalmente `AWS_ENDPOINT`, y define `GAMEVAULT_IMAGE_DISK=s3`.

## 5. Pruebas y datos de rendimiento

```bash
php artisan test
php artisan db:seed --class=PerformanceSeeder
```

La segunda instrucción agrega 500 registros de prueba; ejecútala solo en una base local destinada a pruebas.

## 6. Publicación

Para el paquete de producción:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan storage:link --force
```

Define `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` con HTTPS, `APP_KEY`, `ADMIN_PASSWORD` y `SESSION_SECURE_COOKIE=true` mediante el gestor de secretos del entorno. En Replit, aplica cambios de esquema productivo por el flujo de publicación de la base administrada; no ejecutes `migrate:fresh` en producción.
