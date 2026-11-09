# Mapa de requisitos funcionales

| Requisito | Ruta / acción | Controlador / validación | Vista |
|---|---|---|---|
| RF01 | `GET /` | `CatalogoController@index` | `catalogo.index` |
| RF02 | `GET /?q=` | `CatalogoController@index` | `catalogo.index` |
| RF03 | `GET /?categoria_id=&plataforma_id=&formato=&tipo_proyecto=` | `CatalogoController@index` | `catalogo.index` |
| RF04 | `GET /videojuegos/{slug}` | `CatalogoController@show` | `catalogo.show` |
| RF05 | Enlaces de referencia externa | `CatalogoController@show` | `catalogo.show` |
| RF06 | Banner de advertencia compartido | Layout Blade | `layouts.app` |
| RF07 | Paginación de 12 resultados | `CatalogoController@index` | `catalogo.index` |
| RF08 | `GET/POST /login` | `AuthenticatedSessionController`, `LoginRequest` | `auth.login` |
| RF09 | `POST /logout` | `AuthenticatedSessionController` | `layouts.app` |
| RF10 | `GET/POST /panel/videojuegos` | `VideojuegoController`, `VideojuegoRequest` | `videojuegos.form` |
| RF11 | `GET/PUT /panel/videojuegos/{videojuego}/edit` | `VideojuegoController`, `VideojuegoPolicy` | `videojuegos.form` |
| RF12 | `POST /panel/videojuegos/{videojuego}/imagenes` | `ImagenController`, `ImagenRequest` | `videojuegos.form` |
| RF13 | `DELETE /panel/imagenes/{imagen}` | `ImagenController` | `videojuegos.form` |
| RF14 | CRUD `/panel/categorias` | `CategoriaController`, `CategoriaRequest` | `categorias.index`, `categorias.form` |
| RF15 | CRUD `/panel/plataformas` | `PlataformaController`, `PlataformaRequest` | `plataformas.index`, `plataformas.form` |
| RF16 | `GET /panel`, `GET /panel/videojuegos` | `DashboardController`, `VideojuegoController@index` | `dashboard`, `videojuegos.index` |
| RF17 | `POST .../enviar-revision`, `PATCH .../publicar`, `.../devolver`, `.../despublicar`, `.../retirar` | `VideojuegoController`, `VideojuegoPolicy` | `videojuegos.form` |
| RF18 | `DELETE /panel/videojuegos/{videojuego}` | `VideojuegoController@destroy` | `videojuegos.form` |
| RF19 | CRUD `/panel/videojuegos/{videojuego}/referencias` | `ReferenciaController`, `ReferenciaRequest` | `referencias.index`, `referencias.form` |
| RF20 | Crear / actualizar referencia | `ReferenciaRequest` valida `http` y `https` | `referencias.form` |
| RF21 | `GET/POST /panel/usuarios` | `UsuarioController`, `UsuarioRequest` | `usuarios.index`, `usuarios.form` |
| RF22 | `PATCH /panel/usuarios/{usuario}/estado` | `UsuarioController@cambiarEstado` | `usuarios.index` |
| RF23 | Crear ficha y `POST .../imagenes` | `VideojuegoRequest`, `ImagenRequest` | `videojuegos.form` |
| RF24 | `GET /panel` | `DashboardController@index` | `dashboard` |
| RF25 | `GET /videojuegos/{slug}` | `CatalogoController@show` | `catalogo.show` |
