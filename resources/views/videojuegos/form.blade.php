@extends('layouts.app')
@php($editando = $videojuego->exists)
@section('title', $editando ? 'Editar ficha' : 'Nuevo registro')
@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><p class="eyebrow mb-1">Ficha documental</p><h1 class="h2 fw-bold mb-1">{{ $editando ? 'Editar registro' : 'Nuevo registro' }}</h1><p class="gv-muted mb-0">No agregues archivos de juego, copias ni enlaces de descarga.</p></div>
        <a href="{{ route('videojuegos.index') }}" class="btn btn-outline-light">Volver a registros</a>
    </div>
    @if ($editando)
        <div class="gv-panel mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                <div><span class="small gv-muted">Estado actual</span><div class="mt-1"><span class="badge badge-status status-{{ $videojuego->estado }}">{{ str_replace('_', ' ', ucfirst($videojuego->estado)) }}</span></div></div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-outline-info" href="{{ route('referencias.index', $videojuego) }}">Administrar referencias ({{ $videojuego->referencias->count() }})</a>
                    @can('enviarRevision', $videojuego)
                        <form method="POST" action="{{ route('videojuegos.enviar-revision', $videojuego) }}">@csrf<button class="btn btn-sm btn-gold" type="submit">Enviar a revisión</button></form>
                    @endif
                    @if(auth()->user()->esAdministrador())
                        @if($videojuego->estado === 'en_revision')
                            <form method="POST" action="{{ route('videojuegos.publicar', $videojuego) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-cyan" type="submit">Publicar</button></form>
                            <form method="POST" action="{{ route('videojuegos.devolver', $videojuego) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-light" type="submit">Devolver a borrador</button></form>
                        @elseif($videojuego->estado === 'publicado')
                            <form method="POST" action="{{ route('videojuegos.despublicar', $videojuego) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-light" type="submit">Despublicar</button></form>
                        @endcan
                        @if($videojuego->estado !== 'retirado')
                            <form method="POST" action="{{ route('videojuegos.retirar', $videojuego) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning" type="submit">Retirar</button></form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    <form class="gv-panel" method="POST" action="{{ $editando ? route('videojuegos.update', $videojuego) : route('videojuegos.store') }}" enctype="multipart/form-data">
        @csrf
        @if($editando) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-12 col-lg-8"><label class="form-label" for="titulo">Título</label><input id="titulo" name="titulo" class="form-control" maxlength="180" required value="{{ old('titulo', $videojuego->titulo) }}"></div>
            <div class="col-12 col-lg-4"><label class="form-label" for="categoria_id">Categoría</label><select id="categoria_id" name="categoria_id" class="form-select" required><option value="">Selecciona una categoría</option>@foreach($categorias as $categoria)<option value="{{ $categoria->id }}" @selected(old('categoria_id', $videojuego->categoria_id) == $categoria->id)>{{ $categoria->nombre }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label" for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" rows="4" maxlength="12000" class="form-control" required>{{ old('descripcion', $videojuego->descripcion) }}</textarea></div>
            <div class="col-12 col-md-6"><label class="form-label" for="formato">Formato documentado</label><select id="formato" name="formato" class="form-select" required><option value="">Selecciona</option>@foreach(config('gamevault.formatos') as $opcion)<option value="{{ $opcion }}" @selected(old('formato', $videojuego->formato) === $opcion)>{{ ucfirst($opcion) }}</option>@endforeach</select></div>
            <div class="col-12 col-md-6"><label class="form-label" for="tipo_proyecto">Tipo de proyecto</label><select id="tipo_proyecto" name="tipo_proyecto" class="form-select" required><option value="">Selecciona</option>@foreach(config('gamevault.formatos') as $opcion)<option value="{{ $opcion }}" @selected(old('tipo_proyecto', $videojuego->tipo_proyecto) === $opcion)>{{ ucfirst($opcion) }}</option>@endforeach</select></div>
            <div class="col-12">
                <label class="form-label d-block">Plataformas</label>
                <div class="row g-2">@foreach($plataformas as $plataforma)
                    <div class="col-6 col-md-4 col-lg-3"><label class="form-check gv-panel py-2 px-3"><input class="form-check-input me-2" type="checkbox" name="plataformas[]" value="{{ $plataforma->id }}" @checked(collect(old('plataformas', $videojuego->exists ? $videojuego->plataformas->modelKeys() : []))->contains($plataforma->id))><span class="form-check-label">{{ $plataforma->nombre }}</span></label></div>
                @endforeach</div>
            </div>
            <div class="col-12 col-md-6"><label class="form-label" for="informacion_tecnica">Información técnica <span class="gv-muted">(opcional)</span></label><textarea id="informacion_tecnica" name="informacion_tecnica" rows="4" maxlength="12000" class="form-control">{{ old('informacion_tecnica', $videojuego->informacion_tecnica) }}</textarea></div>
            <div class="col-12 col-md-6"><label class="form-label" for="requisitos_conocidos">Requisitos conocidos <span class="gv-muted">(opcional)</span></label><textarea id="requisitos_conocidos" name="requisitos_conocidos" rows="4" maxlength="12000" class="form-control">{{ old('requisitos_conocidos', $videojuego->requisitos_conocidos) }}</textarea></div>
            <div class="col-12 col-md-6"><label class="form-label" for="fecha_lanzamiento">Fecha documentada <span class="gv-muted">(opcional)</span></label><input id="fecha_lanzamiento" name="fecha_lanzamiento" type="date" class="form-control" value="{{ old('fecha_lanzamiento', $videojuego->fecha_lanzamiento?->format('Y-m-d')) }}"></div>
            <div class="col-12 col-md-6"><label class="form-label" for="advertencia">Advertencia editorial</label><textarea id="advertencia" name="advertencia" rows="2" maxlength="3000" class="form-control" required>{{ old('advertencia', $videojuego->advertencia ?: config('gamevault.advertencia')) }}</textarea></div>

            @if(!$editando)
                <div class="col-12">
                    <label class="form-label" for="imagenes">Portada y otras imágenes <span class="gv-muted">(JPG, PNG o WEBP; máximo 2 MB cada una)</span></label>
                    <input id="imagenes" name="imagenes[]" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple required class="form-control" data-image-preview>
                    <div class="row g-3 mt-1" data-preview-grid aria-live="polite"></div>
                    <div class="form-text text-light-emphasis">Selecciona al menos una imagen. La de menor orden se usará como portada. No subas juegos, instaladores, ROM ni archivos ejecutables.</div>
                </div>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button class="btn btn-cyan" type="submit">{{ $editando ? 'Guardar cambios' : 'Crear borrador' }}</button>
            @if($editando)<a class="btn btn-outline-light" href="{{ route('videojuegos.show', $videojuego) }}">Ver ficha</a>@endif
        </div>
    </form>

    @if($editando)
        <section class="gv-panel mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><div><p class="eyebrow mb-1">Material visual</p><h2 class="h5 mb-0">Imágenes de la ficha</h2></div><span class="small gv-muted">La de menor orden es la portada</span></div>
            <div class="row g-3 mb-4">
                @forelse($videojuego->imagenes as $imagen)
                    <div class="col-6 col-md-4 col-lg-3">
                        <img class="preview-image" src="{{ \Illuminate\Support\Facades\Storage::disk(config('gamevault.image_disk'))->url($imagen->ruta) }}" alt="{{ $imagen->texto_alternativo }}">
                        <p class="small gv-muted mt-2 mb-2">{{ $imagen->texto_alternativo }} · orden {{ $imagen->orden }}</p>
                        <form method="POST" action="{{ route('imagenes.destroy', $imagen) }}" onsubmit="return confirm('¿Eliminar esta imagen?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar imagen</button></form>
                    </div>
                @empty
                    <p class="gv-muted">No hay imágenes registradas.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('imagenes.store', $videojuego) }}" enctype="multipart/form-data" class="border-top border-secondary-subtle pt-3">
                @csrf
                <label class="form-label" for="mas-imagenes">Agregar imágenes (JPG, PNG o WEBP; máximo 2 MB cada una)</label>
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-8"><input id="mas-imagenes" name="imagenes[]" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple required class="form-control" data-image-preview></div>
                    <div class="col-12 col-md-4"><label class="form-label" for="orden">Orden inicial</label><input id="orden" name="orden" type="number" min="0" value="{{ $videojuego->imagenes->max('orden') + 1 }}" class="form-control"></div>
                </div>
                <div class="row g-3 mt-1" data-preview-grid aria-live="polite"></div>
                <button class="btn btn-outline-info btn-sm mt-3" type="submit">Cargar imágenes</button>
            </form>
        </section>
    @endif

    @if($videojuego->exists && auth()->user()->esAdministrador())
        <form method="POST" action="{{ route('videojuegos.destroy', $videojuego) }}" class="mt-4" onsubmit="return confirm('¿Eliminar esta ficha, todas sus imágenes almacenadas y sus referencias? Esta acción no se puede deshacer.')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger" type="submit">Eliminar ficha y archivos</button>
        </form>
    @endif
@endsection
