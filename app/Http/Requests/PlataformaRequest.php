<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlataformaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador' && $this->user()->activo;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $plataforma = $this->route('plataforma');

        return [
            'nombre' => ['required', 'string', 'max:120', Rule::unique('plataformas', 'nombre')->ignore($plataforma)],
            'tipo' => ['required', 'string', 'max:80'],
        ];
    }
}
