<?php

namespace App\Http\Requests;

use App\Models\Videojuego;
use Illuminate\Foundation\Http\FormRequest;

class ImagenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $videojuego = $this->route('videojuego');

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
            'imagenes' => ['required_without:imagen_urls', 'array', 'min:1', 'max:12'],
            'imagenes.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'imagen_urls' => ['required_without:imagenes', 'array', 'min:1', 'max:12'],
            'imagen_urls.*' => ['required', 'url:http,https', 'max:2048'],
            'textos_alternativos' => ['nullable', 'array'],
            'textos_alternativos.*' => ['nullable', 'string', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
