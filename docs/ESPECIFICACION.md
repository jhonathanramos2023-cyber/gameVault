# GameVault: especificación obligatoria

Aplicación web con Laravel (última versión estable compatible con PHP 8.3), Blade, HTML5, CSS, JavaScript, Bootstrap 5 y Laravel Breeze (stack Blade). Proyecto de cátedra de la Universidad Luterana Salvadoreña (asignatura Proyecto de Aplicaciones Web). Se publicará en GitHub y como sitio web en línea. Interfaz, mensajes y comentarios del código en español. No agregar funcionalidades que no estén en este documento. Si algo no es posible, decirlo de forma explícita en vez de sustituirlo en silencio.

## 1. Propósito y regla inquebrantable

GameVault es un catálogo informativo y educativo que documenta videojuegos modificados, copias digitales y proyectos no oficiales (cracks, ROM, ISO, repacks, ports, emulación, reconstrucción/ingeniería inversa, preservación digital). NO aloja, almacena ni distribuye juegos, ejecutables, ROM, ISO ni cracks. No existen botones de descarga. Los enlaces son solo referencias documentales (título + URL) con advertencia visible.

Fuera de alcance, no agregar: streaming, ejecución de juegos, emulación en la nube, antivirus, certificación de archivos, scraping, descargas automáticas, pagos, carrito, reseñas públicas, app móvil, verificación automática de disponibilidad de URL.

## 2. Base de datos

Debe funcionar con PostgreSQL, MySQL/MariaDB y SQLite cambiando solo el .env. Migraciones portables: columnas string con validación en vez de enum de BD, sin SQL específico de un motor. La búsqueda debe ser insensible a mayúsculas y portable (LOWER(columna) LIKE LOWER(?)). .env.example con ejemplos comentados de pgsql, mysql y sqlite.

## 3. Tablas (exactamente estas 7, además de las propias de Laravel)

- **users**: id, name, email, password, rol (colaborador|administrador), activo (boolean, default true), timestamps
- **videojuegos**: id, titulo, slug (único), descripcion, informacion_tecnica (text, nullable), requisitos_conocidos (text, nullable), formato, tipo_proyecto, estado (borrador|en_revision|publicado|retirado), advertencia, fecha_lanzamiento (nullable), categoria_id (FK), user_id (FK), timestamps
- **categorias**: id, nombre, slug, descripcion, timestamps
- **plataformas**: id, nombre, tipo, timestamps
- **videojuego_plataforma**: videojuego_id, plataforma_id (clave primaria compuesta, FKs)
- **imagenes_videojuego**: id, videojuego_id (FK, cascade), ruta, texto_alternativo, orden, timestamps
- **referencias**: id, videojuego_id (FK, cascade), titulo, url, tipo_fuente, observaciones, fecha_consulta, timestamps

Relaciones Eloquent: User 1:N Videojuego; Categoria 1:N Videojuego; Videojuego N:M Plataforma; Videojuego 1:N Imagen y 1:N Referencia.

Valores para formato y tipo_proyecto: crack/modificación, ROM, imagen ISO, repack, port o adaptación, emulación, reconstrucción/ingeniería inversa, preservación digital, otro.

Incluir migraciones, modelos, seeders y factories para todos los modelos.

## 4. Roles

- **Visitante**: sin cuenta. Solo consulta lo publicado.
- **Colaborador**: inicia y cierra sesión, registra videojuegos, edita solo los suyos mientras estén en borrador, carga imágenes, añade y actualiza referencias, consulta el estado de sus registros. No publica, no administra cuentas ni ve registros ajenos.
- **Administrador**: todo lo anterior sobre cualquier registro, más publicar/despublicar/retirar, eliminar con confirmación, gestionar categorías, plataformas y colaboradores, y consultar el panel de resumen.

Sin registro público: eliminar las rutas de registro de Breeze y la opción de eliminar la cuenta propia del perfil. Solo el administrador crea cuentas de colaborador. Los usuarios inactivos no pueden iniciar sesión. Middleware de rol (alias rol) y una Policy para videojuegos.

## 5. Flujo editorial

Colaborador crea (borrador) → botón "Enviar a revisión" (en_revision) → administrador publica (publicado) o devuelve (borrador). Despublicar vuelve a borrador; retirar pasa a retirado. Solo "publicado" es visible al público. Bloquear transiciones inválidas en servidor.

## 6. Estructura MVC

Modelos: User, Videojuego, Categoria, Plataforma, ImagenVideojuego, Referencia. Controladores: CatalogoController (index, show), DashboardController, VideojuegoController (resource + enviarRevision, publicar, devolver, despublicar, retirar), CategoriaController, PlataformaController, UsuarioController, ImagenController, ReferenciaController. Un Form Request por entidad. Rutas nombradas, agrupadas por middleware (público, auth, rol:administrador). Comentar en español el código clave (middleware, policy, Form Requests, relaciones) para que el equipo pueda explicarlo.

## 7. Sitio público

Inicio: catálogo en tarjetas (portada, título, clasificación), búsqueda por título/palabras clave, filtros por categoría, plataforma, formato y tipo de proyecto, paginación de 12, carga anticipada (with).

Ficha `/videojuegos/{slug}`: portada, galería, descripción, información técnica, requisitos conocidos, plataformas, clasificación, estado, advertencia y referencias externas (`target="_blank"`, `rel="noopener noreferrer"`).

Banner visible en catálogo y fichas: "Las referencias externas pertenecen a terceros. GameVault no analiza ni certifica la seguridad, legalidad ni disponibilidad de sus sitios y archivos, y no ofrece asesoría legal."

Pie de página: proyecto de cátedra ULS y créditos de las herramientas externas usadas (Laravel, Breeze, Bootstrap).

## 8. Panel (colaborador y administrador)

Dashboard del administrador con totales de registros, borradores, publicados y usuarios activos. CRUD de videojuegos, categorías, plataformas, imágenes y referencias. Eliminar un videojuego pide confirmación (JavaScript) y borra sus imágenes (archivos incluidos) y referencias. Categorías y plataformas con registros asociados no se pueden eliminar: mensaje claro.

Imágenes: varias por videojuego, JPG/PNG/WEBP, máximo 2 MB, texto alternativo y orden, vista previa con JavaScript antes de subir. La portada es la imagen de menor orden. El disco de almacenamiento debe ser configurable por variable de entorno (local por defecto; compatible con almacenamiento S3 en producción).

Referencias: título, URL (validar formato), tipo de fuente, observaciones y fecha de consulta.

Mensajes de confirmación y de error visibles en toda operación. Laravel en español (APP_LOCALE=es, archivos lang en español, mensajes de validación claros).

## 9. Requerimientos funcionales (implementar los 25)

RF01 listado de publicados con portada, título y clasificación. RF02 búsqueda por título o palabras clave. RF03 filtros por categoría, plataforma, formato y tipo. RF04 detalle con descripción, plataformas, formato, imágenes, advertencias y referencias. RF05 referencias externas documentales. RF06 advertencia general de riesgos de archivos de terceros. RF07 paginación. RF08 inicio de sesión con correo y contraseña. RF09 cierre de sesión. RF10 registrar videojuego con título, descripción, plataforma, categoría, formato, tipo y portada. RF11 colaborador edita lo suyo, administrador todo. RF12 cargar varias imágenes. RF13 eliminar imágenes. RF14 administrar categorías. RF15 administrar plataformas. RF16 colaborador consulta sus registros y estado. RF17 administrador publica, despublica o retira. RF18 eliminar con confirmación. RF19 añadir referencias. RF20 validar formato de URL. RF21 administrador crea colaboradores. RF22 activar y desactivar colaboradores. RF23 validar tipo y tamaño de imágenes. RF24 resumen para el administrador. RF25 clasificación y advertencias editoriales en cada ficha.

## 10. Requerimientos no funcionales

Responsivo con Bootstrap 5, un registro publicado localizable en máximo 3 interacciones. Hash de contraseñas de Laravel, middleware, CSRF, escape en Blade, validación en servidor con Form Requests, consultas con Eloquent. MVC con rutas nombradas, controladores, modelos con relaciones, migraciones, seeders y factories. Llaves foráneas. Chrome, Firefox y Edge. Control de versiones con Git.

## 11. Seeders, factories y pruebas

Seeders: 1 administrador, 2 colaboradores, unas 8 categorías, unas 8 plataformas y unos 12 videojuegos de ejemplo. Usar SOLO proyectos legales y abiertos (ScummVM, OpenMW, OpenRA, OpenTTD, GZDoom, DOSBox) con referencias únicamente a sus sitios oficiales. Ningún enlace a descargas de material protegido. Imágenes de ejemplo: placeholders sin material con derechos de autor.

PerformanceSeeder con factories que genere 500 registros para pruebas locales.

Pruebas automáticas (feature tests, SQLite en memoria) que cubran: (1) el administrador inicia sesión; (2) un colaborador crea un borrador y lo envía a revisión; (3) el administrador lo publica; (4) un visitante lo ve; (5) búsqueda y filtros; (6) imagen válida aceptada e inválida rechazada; (7) URL mal escrita rechazada; (8) usuario inactivo no entra; (9) eliminar borra imágenes y referencias; (10) un colaborador no puede editar registros ajenos ni entrar a rutas de administrador.

## 12. Publicación (GitHub y sitio en línea)

Repositorio listo para GitHub: .gitignore de Laravel, .env.example, sin secretos en el código y sin .env versionado.

Producción: APP_ENV=production, APP_DEBUG=false, HTTPS forzado cuando APP_ENV=production, proxies de confianza configurados, cookies de sesión seguras.

El administrador en producción sale de la variable ADMIN_PASSWORD; APP_KEY es secreto. Sin credenciales fijas en el sitio público.

El arranque en producción ejecuta `php artisan migrate --force` (nunca migrate:fresh). Los seeders son idempotentes. Las cuentas demo de colaborador solo se crean si APP_ENV no es production.

Las imágenes de ejemplo viven dentro del repositorio (public/) para persistir entre despliegues. Las subidas de usuarios usan almacenamiento configurable (S3-compatible).

El hosting se decide al final, después de que todo lo anterior pase.

## 13. Documentación a entregar

README.md: descripción, funcionalidades, tecnologías, instalación y ejecución local (PostgreSQL, MySQL y SQLite), credenciales de prueba (solo local) y créditos de recursos externos y herramientas de IA usadas.

MANUAL.md: (a) instalación y ejecución paso a paso (composer install, .env, php artisan key:generate, php artisan migrate --seed, php artisan storage:link, npm install, npm run dev, php artisan serve); (b) uso por rol (visitante, colaborador, administrador).

docs/MAPA_RF.md: tabla que relacione cada RF01–RF25 con su ruta, controlador y vista.

## 14. Estilo visual

Tema oscuro hacker/pirata underground con est�tica de videojuego prohibido. Paleta: fondo #020a02, verde matrix #00ff41, verde oscuro #0b3d0b, rojo #ff2a2a, �mbar #ffb000. Tipograf�as: Orbitron para t�tulos y Share Tech Mono para texto terminal. Efectos: lluvia de unos y ceros (Matrix), glitch en t�tulos, ne�n con text-shadow y box-shadow, scanlines, paneles de cristal con backdrop-filter, animaciones de entrada. Zona verde: cat�logo, login, registros, categor�as, plataformas, referencias. Zona roja: panel y colaboradores. Fondo din�mico por secci�n con im�genes de public/img/fondos/ (principal, login, panel, registros, categorias, colaboradores, referencias). Responsive con efectos reducidos en m�vil (375px) y soporte para prefers-reduced-motion.

## 15. Verificación final (obligatoria)

Ejecutar `php artisan migrate:fresh --seed` en SQLite y en MySQL, `php artisan test` y `npm run build`. Informar qué pasa y qué no. No declarar algo como terminado si no se ejecutó.

## 16. Estado conocido

Comprobado en local (Windows, PHP 8.3, SQLite): `migrate:fresh --seed` sin errores, 13 tests aprobados (45 comprobaciones), `npm run build` correcto, catálogo con 12 fichas de ejemplo funcionando, panel de administración accesible, y limpieza de archivos sobrantes de Breeze y Replit (sin rutas de registro, perfil, recuperación de contraseña ni verificación de correo).

Corregido a mano: botón de eliminar que rompía la pantalla de crear videojuego; el administrador ahora puede enviar sus propios borradores a revisión (Policy `enviarRevision`).

Sin verificar: JavaScript de vista previa de imágenes y confirmación al eliminar; filtros en móvil; HTTPS forzado, proxies de confianza y cookies seguras; README específico del proyecto; ejecución con MySQL y PostgreSQL; que Tailwind y Bootstrap no se carguen a la vez.

Pendiente: publicación del sitio (no realizada).
