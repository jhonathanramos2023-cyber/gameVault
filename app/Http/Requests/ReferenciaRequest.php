<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Referencia;
use App\Models\Videojuego;

class ReferenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $referencia = $this->route('referencia');
        $videojuego = $this->route('videojuego');

        if ($referencia instanceof Referencia) {
            return $this->user()?->can('update', $referencia->videojuego) ?? false;
        }

        return $videojuego instanceof Videojuego
            && ($this->user()?->can('update', $videojuego) ?? false);
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
            'url' => ['required', 'url:http,https', 'max:2048'],
            'tipo_fuente' => ['required', 'string', 'max:80'],
            'observaciones' => ['nullable', 'string', 'max:3000'],
            'fecha_consulta' => ['nullable', 'date'],
        ];
    }
}
