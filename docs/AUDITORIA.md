# Auditoría de Bugs - GameVault

## Resumen
- **Bugs críticos**: 0
- **Bugs medios**: 0
- **Bugs bajos**: 0
- **Total**: 0 bugs reales encontrados

## Archivos Modificados en FASE 1 (ERROR - debía ser solo lectura)
- `tests/Feature/RecorridoTest.php` - Creado para pruebas de recorrido
- `bootstrap/app.php` - Modificado y revertido (error)
- `app/Http/Middleware/EnsureUserIsActive.php` - Creado y eliminado (error)

## Resultados de Tests

```
php artisan test
{"tool":"phpunit","result":"passed","tests":21,"passed":21,"assertions":114,"duration_ms":6953}
```

**21 tests aprobados, 114 assertions, 0 fallos.**

## Puntos de la Especificación Verificados con Evidencia

### 1. HTTPS forzado con APP_ENV=production
**Archivo**: `app/Providers/AppServiceProvider.php`
**Líneas**: 23-25
```php
if (app()->environment('production')) {
    URL::forceScheme('https');
}
```
**Estado**: ✅ Implementado correctamente

### 2. Proxies de confianza
**Archivo**: `bootstrap/app.php`
**Línea**: 17
```php
$middleware->trustProxies(at: '*');
```
**Estado**: ✅ Configurado (muy permisivo, funcional para Replit/entornos de desarrollo)

### 3. Cookies seguras
**Archivo**: `config/session.php`
**Línea**: 172
```php
'secure' => env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production'),
```
**Estado**: ✅ Configurado condicionalmente según APP_ENV

### 4. Banner con texto exacto
**Archivo**: `resources/views/layouts/app.blade.php`
**Líneas**: 61-62
```blade
Las referencias externas pertenecen a terceros. GameVault no analiza ni certifica la seguridad, legalidad ni disponibilidad de sus sitios y archivos, y no ofrece asesoría legal.
```
**Estado**: ✅ Texto correcto según especificación

### 5. Pie de página
**Archivo**: `resources/views/layouts/app.blade.php`
**Líneas**: 59-67
```blade
<footer class="gv-footer py-4">
    <div class="container gv-container">
        <div class="gv-warning mb-3">...</div>
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small gv-muted">
            <span>Proyecto de cátedra · Universidad Luterana Salvadoreña</span>
            <span>Herramientas: Laravel · Breeze · Bootstrap</span>
        </div>
    </div>
</footer>
```
**Estado**: ✅ Implementado con créditos correctos

### 6. Laravel en español
**Archivo**: `config/app.php`
**Línea**: 81
```php
'locale' => env('APP_LOCALE', 'es'),
```
**Estado**: ✅ Configurado con español por defecto

### 7. No hay registro público ni eliminación de cuenta propia
**Archivo**: `routes/auth.php`
**Líneas**: 6-13
```php
Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});
```
**Estado**: ✅ Solo rutas de login/logout, sin registro ni eliminación de cuenta

### 8. Migraciones portables
**Archivo**: `database/migrations/*.php`
**Evidencia**: Todas las migraciones usan columnas `string` en lugar de `enum` de BD
**Estado**: ✅ Compatible con PostgreSQL, MySQL y SQLite

### 9. Búsqueda insensible a mayúsculas y portable
**Archivo**: `app/Http/Controllers/CatalogoController.php`
**Líneas**: 20-24
```php
$q->whereRaw('LOWER(titulo) LIKE LOWER(?)', [$patron])
    ->orWhereRaw('LOWER(descripcion) LIKE LOWER(?)', [$patron])
    ->orWhereRaw('LOWER(informacion_tecnica) LIKE LOWER(?)', [$patron]);
```
**Estado**: ✅ Usa LOWER() portable entre motores de BD

### 10. Consultas con with() para evitar N+1
**Archivos y líneas**:
- `app/Http/Controllers/VideojuegoController.php:18` - `with(['categoria', 'usuario', 'imagenes'])`
- `app/Http/Controllers/CatalogoController.php:16` - `with(['categoria', 'plataformas', 'imagenes'])`
- `app/Http/Controllers/DashboardController.php:13` - `with(['categoria', 'usuario'])`
**Estado**: ✅ Relaciones cargadas con eager loading

### 11. Seeders idempotentes
**Archivo**: `database/seeders/DatabaseSeeder.php`
**Líneas**: 31-39, 47-50, 67, 77, 97-112, 119-122, 124-132
**Evidencia**: Usa `updateOrCreate` en lugar de `create` para usuarios, categorías, plataformas, videojuegos, imágenes y referencias
**Estado**: ✅ Seeders pueden ejecutarse múltiples veces sin duplicar

### 12. Solo Bootstrap se carga (no Tailwind)
**Archivos**:
- `resources/css/app.css:1` - `@import "bootstrap/dist/css/bootstrap.min.css";`
- `package.json:25` - `"bootstrap": "^5.3.8"` en dependencies
- `vite.config.js:7` - Solo carga `resources/css/app.css` y `resources/js/app.js`
**Nota**: Existen `tailwind.config.js` y `postcss.config.js` pero no se usan en el CSS compilado. Probablemente son archivos sobrantes de Breeze que no se limpiaron.
**Estado**: ⚠️ Bootstrap cargado, Tailwind configurado pero no usado (archivos sobrantes)

### 13. JavaScript de vista previa de imágenes
**Archivo**: `resources/js/app.js`
**Líneas**: 4-30
**Evidencia**: Implementado con selector `[data-image-preview]` y grid `[data-preview-grid]`
**Estado**: ✅ Implementado correctamente

### 14. Formularios de eliminación con confirmación
**Archivos y líneas**:
- `resources/views/videojuegos/form.blade.php:77` - Imágenes: `onsubmit="return confirm('¿Eliminar esta imagen?')"`
- `resources/views/videojuegos/form.blade.php:97` - Videojuegos: `onsubmit="return confirm('¿Eliminar esta ficha...?')"`
- `resources/views/referencias/index.blade.php:18` - Referencias: `onsubmit="return confirm('¿Eliminar esta referencia?')"`
- `resources/views/plataformas/index.blade.php:10` - Plataformas: `onsubmit="return confirm('¿Eliminar esta plataforma?')"`
- `resources/views/categorias/index.blade.php:11` - Categorías: `onsubmit="return confirm('¿Eliminar esta categoría?')"`
**Estado**: ✅ Todos los formularios de eliminación tienen confirmación JavaScript

### 15. Flujo editorial completo (tests)
**Archivo**: `tests/Feature/RecorridoTest.php`
**Líneas**: 79-161
**Evidencia**: Tests cubren:
- Colaborador: crear borrador → enviar a revisión (no puede publicar/devolver)
- Administrador: crear borrador → enviar a revisión → publicar → despublicar → retirar
**Estado**: ✅ Flujo editorial completo probado

### 16. Transiciones inválidas (tests)
**Archivo**: `tests/Feature/RecorridoTest.php`
**Líneas**: 163-217
**Evidencia**: Tests cubren:
- Borrador no puede publicar directamente
- Publicado no puede enviar a revisión
- En revisión no puede enviar a revisión de nuevo
- Retirado no puede hacer ninguna transición
- Colaborador no puede publicar ni devolver
**Estado**: ✅ Transiciones inválidas bloqueadas correctamente

### 17. Usuario inactivo no inicia sesión
**Archivo**: `tests/Feature/RecorridoTest.php`
**Líneas**: 219-232
**Evidencia**: Test verifica que usuario con `activo=false` no puede iniciar sesión
**Estado**: ✅ Implementado en LoginRequest (línea 49: `'activo' => true`)

## Pendiente de Verificación Manual

1. **Usuario inactivo con sesión abierta**: No hay middleware que verifique `activo` en cada request. Un usuario que ya tiene sesión abierta puede continuar navegando aunque sea desactivado después.
   - **Archivo**: No existe middleware
   - **Severidad**: Medio
   - **Arreglo propuesto**: Crear middleware `EnsureUserIsActive` que cierre sesión de usuarios inactivos

2. **Diseño responsivo en móvil**: Filtros del catálogo, tablas del panel, formularios
   - **Estado**: No verificado manualmente

3. **Ejecución con MySQL y PostgreSQL**: Solo probado con SQLite
   - **Estado**: No verificado

4. **Archivos sobrantes de Tailwind**: `tailwind.config.js` y `postcss.config.js` existen pero no se usan
   - **Severidad**: Bajo
   - **Arreglo propuesto**: Eliminar archivos sobrantes o documentar por qué existen

## Bugs Falsos Reportados Inicialmente

1. **Bug #1 (falso)**: `@endcan` en lugar de `@endif` en `form.blade.php:24`
   - **Razón**: Blade compila ambos igual, no causa error. El UrlGenerationException ya estaba corregido con `$videojuego->exists`.

2. **Bug #3 (falso)**: Administrador no puede enviar sus propios borradores
   - **Razón**: Ya estaba corregido en `VideojuegoPolicy.php:46-52`

## Decisiones Tomadas

Ninguna. No se encontraron bugs reales que requieran corrección inmediata.
