<?php

namespace App\Http\Requests;

use App\Models\Videojuego;
use Illuminate\Foundation\Http\FormRequest;

class VideojuegoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(
            $this->isMethod('POST') ? 'create' : 'update',
            $this->isMethod('POST') ? Videojuego::class : $this->route('videojuego')
        ) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['required', 'string', 'max:12000'],
            'informacion_tecnica' => ['nullable', 'string', 'max:12000'],
            'requisitos_conocidos' => ['nullable', 'string', 'max:12000'],
            'formato' => ['required', 'string', 'in:crack/modificación,ROM,imagen ISO,repack,port o adaptación,emulación,reconstrucción/ingeniería inversa,preservación digital,otro'],
            'tipo_proyecto' => ['required', 'string', 'in:crack/modificación,ROM,imagen ISO,repack,port o adaptación,emulación,reconstrucción/ingeniería inversa,preservación digital,otro'],
            'advertencia' => ['required', 'string', 'max:3000'],
            'fecha_lanzamiento' => ['nullable', 'date'],
            'url_iso' => ['nullable', 'url', 'max:2048'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'plataformas' => ['required', 'array', 'min:1'],
            'plataformas.*' => ['integer', 'distinct', 'exists:plataformas,id'],
            'imagenes' => [$this->isMethod('POST') ? 'required' : 'sometimes', 'array', 'min:1', 'max:12'],
            'imagenes.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'textos_alternativos' => ['nullable', 'array'],
            'textos_alternativos.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
