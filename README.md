# GameVault

GameVault es un catálogo informativo y educativo que documenta videojuegos modificados, copias digitales y proyectos no oficiales (cracks, ROM, ISO, repacks, ports, emulación, reconstrucción/ingeniería inversa, preservación digital). Es un proyecto de cátedra de la Universidad Luterana Salvadoreña (asignatura Proyecto de Aplicaciones Web).

**Importante**: GameVault NO aloja, almacena ni distribuye juegos, ejecutables, ROM, ISO ni cracks. Los enlaces son solo referencias documentales con advertencia visible.

## Funcionalidades

- **Catálogo público**: Consulta de videojuegos publicados con búsqueda por título/palabras clave, filtros por categoría, plataforma, formato y tipo de proyecto, y paginación.
- **Fichas detalladas**: Información técnica, requisitos conocidos, plataformas, imágenes, advertencias editoriales y referencias externas documentales.
- **Flujo editorial**: Colaboradores crean borradores → envían a revisión → administrador publica o devuelve → puede despublicar o retirar.
- **Gestión de contenido**: CRUD de videojuegos, categorías, plataformas, imágenes y referencias.
- **Gestión de usuarios**: Solo el administrador crea cuentas de colaborador y puede activar/desactivar cuentas.
- **Seguridad**: Usuarios inactivos no pueden iniciar sesión ni continuar con sesión abierta.

## Tecnologías

- **Backend**: Laravel (última versión compatible con PHP 8.3)
- **Frontend**: Blade, HTML5, CSS, JavaScript, Bootstrap 5
- **Autenticación**: Laravel Breeze (stack Blade)
- **Base de datos**: Compatible con PostgreSQL, MySQL/MariaDB y SQLite
- **Pruebas**: PHPUnit con SQLite en memoria

## Instalación y Ejecución Local

### Requisitos Previos

- PHP 8.3 o superior
- Composer
- Node.js y npm
- SQLite (por defecto) o PostgreSQL/MySQL

### Instalación

```bash
# Clonar el repositorio
git clone <repositorio>
cd GameVault

# Instalar dependencias de PHP
composer install

# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Crear enlace de almacenamiento
php artisan storage:link

# Instalar dependencias de JavaScript
npm install

# Compilar assets
npm run build
```

### Configuración de Base de Datos

El proyecto funciona con SQLite por defecto. Para usar PostgreSQL o MySQL, modifica el archivo `.env`:

**SQLite (por defecto)**:
```env
DB_CONNECTION=sqlite
# DB_DATABASE no es necesario para SQLite
```

**PostgreSQL**:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gamevault
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

**MySQL/MariaDB**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gamevault
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

### Ejecución

```bash
# Iniciar servidor de desarrollo
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

### Ejecutar Tests

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests con cobertura (opcional)
php artisan test --coverage
```

## Credenciales de Prueba (Solo Local)

**Administrador**:
- Email: `admin@gamevault.local`
- Contraseña: `GameVaultAdmin2026!`

**Colaboradores** (solo en entorno local, no en producción):
- Colaborador 1: `colaborador1@gamevault.local` / `GameVaultLocal2026!`
- Colaborador 2: `colaborador2@gamevault.local` / `GameVaultLocal2026!`

**Nota**: En producción, el administrador debe definir `ADMIN_PASSWORD` en el archivo `.env` antes de ejecutar `php artisan migrate --seed`.

## Créditos

### Recursos Externos

- **Laravel**: Framework PHP
- **Laravel Breeze**: Sistema de autenticación
- **Bootstrap 5**: Framework CSS

### Herramientas de IA Utilizadas

- **Replit Agent**: Desarrollo inicial del proyecto
- **Cursor**: Edición y corrección de código
- **Claude (Anthropic)**: Asistencia en desarrollo, debugging y documentación

## Documentación

- **Especificación**: `docs/ESPECIFICACION.md` - Especificación completa del proyecto
- **Manual**: `MANUAL.md` - Manual de instalación y uso paso a paso
- **Mapa RF**: `docs/MAPA_RF.md` - Relación entre requerimientos funcionales y rutas/controladores

## Licencia

Este es un proyecto académico de la Universidad Luterana Salvadoreña.
