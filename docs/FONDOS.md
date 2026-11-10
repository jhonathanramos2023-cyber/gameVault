# Fondos de Sección - GameVault

## Tabla de Sección a Archivo

| Sección | Archivo de Fondo | Descripción |
|---------|-----------------|-------------|
| principal (catálogo) | public/img/fondos/principal.jpg | Imagen principal del catálogo |
| login | public/img/fondos/login.jpg | Fondo de la página de login |
| panel (dashboard) | public/img/fondos/panel.jpg | Fondo del panel de administración |
| registros (videojuegos) | public/img/fondos/registros.jpg | Fondo de gestión de videojuegos |
| categorias | public/img/fondos/categorias.jpg | Fondo de gestión de categorías |
| colaboradores (usuarios) | public/img/fondos/colaboradores.jpg | Fondo de gestión de colaboradores |
| referencias | public/img/fondos/referencias.jpg | Fondo de gestión de referencias |
| plataformas | (sin imagen) | Fondo generado con CSS (líneas de circuito animadas) |

## Nota Importante

Las imágenes con derechos de autor son responsabilidad del equipo del proyecto. GameVault no proporciona estas imágenes; deben ser proporcionadas por el equipo y deben cumplir con las leyes de derechos de autor aplicables.

## Configuración

Los fondos se cargan dinámicamente en `resources/views/layouts/app.blade.php` usando el atributo `data-fondo` en el elemento `<body>`. Si el archivo de imagen existe en `public/img/fondos/{seccion}.jpg`, se muestra como fondo fijo con una capa oscura superpuesta para garantizar la legibilidad del contenido.

## Opacidad de Capa Oscura

- **principal** y **login**: 35% opacidad (fondo más visible)
- **registros**, **categorias**, **referencias**, **panel**, **colaboradores**: 75% opacidad (fondo más oscuro para legibilidad de tablas y formularios)
- **plataformas**: Sin imagen, fondo generado con CSS
