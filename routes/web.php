<?php

use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\PlataformaController;
use App\Http\Controllers\ReferenciaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VideojuegoController;
use Illuminate\Support\Facades\Route;

// La consulta pública solo obtiene proyectos publicados; la ficha usa slug legible.
Route::get('/', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/videojuegos/{videojuego:slug}', [CatalogoController::class, 'show'])->name('catalogo.show');

Route::middleware(['auth', 'rol:administrador,colaborador'])->group(function (): void {
    Route::get('/panel', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('panel')->group(function (): void {
        Route::resource('videojuegos', VideojuegoController::class)
            ->except(['show'])
            ->parameters(['videojuegos' => 'videojuego'])
            ->names('videojuegos');
        Route::get('videojuegos/{videojuego}/ficha', [VideojuegoController::class, 'show'])->name('videojuegos.show');
        Route::post('videojuegos/{videojuego}/enviar-revision', [VideojuegoController::class, 'enviarRevision'])->name('videojuegos.enviar-revision');
        Route::patch('videojuegos/{videojuego}/publicar', [VideojuegoController::class, 'publicar'])->middleware('rol:administrador')->name('videojuegos.publicar');
        Route::patch('videojuegos/{videojuego}/devolver', [VideojuegoController::class, 'devolver'])->middleware('rol:administrador')->name('videojuegos.devolver');
        Route::patch('videojuegos/{videojuego}/despublicar', [VideojuegoController::class, 'despublicar'])->middleware('rol:administrador')->name('videojuegos.despublicar');
        Route::patch('videojuegos/{videojuego}/retirar', [VideojuegoController::class, 'retirar'])->middleware('rol:administrador')->name('videojuegos.retirar');

        Route::post('videojuegos/{videojuego}/imagenes', [ImagenController::class, 'store'])->name('imagenes.store');
        Route::delete('imagenes/{imagen}', [ImagenController::class, 'destroy'])->name('imagenes.destroy');

        Route::get('videojuegos/{videojuego}/referencias', [ReferenciaController::class, 'index'])->name('referencias.index');
        Route::get('videojuegos/{videojuego}/referencias/crear', [ReferenciaController::class, 'create'])->name('referencias.create');
        Route::post('videojuegos/{videojuego}/referencias', [ReferenciaController::class, 'store'])->name('referencias.store');
        Route::get('videojuegos/{videojuego}/referencias/{referencia}/editar', [ReferenciaController::class, 'edit'])->name('referencias.edit');
        Route::put('videojuegos/{videojuego}/referencias/{referencia}', [ReferenciaController::class, 'update'])->name('referencias.update');
        Route::delete('videojuegos/{videojuego}/referencias/{referencia}', [ReferenciaController::class, 'destroy'])->name('referencias.destroy');
    });

    Route::middleware('rol:administrador')->prefix('panel')->group(function (): void {
        Route::resource('categorias', CategoriaController::class)->except(['show'])->names('categorias');
        Route::resource('plataformas', PlataformaController::class)->except(['show'])->names('plataformas');
        Route::resource('usuarios', UsuarioController::class)->except(['show', 'destroy'])->names('usuarios');
        Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');
    });
});

require __DIR__.'/auth.php';
