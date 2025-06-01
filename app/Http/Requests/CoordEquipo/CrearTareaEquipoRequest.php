<?php

namespace App\Http\Requests\CoordEquipo;

use Illuminate\Foundation\Http\FormRequest;

class CrearTareaEquipoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'meta_id' => 'required|exists:metas,id',
            'fecha_entrega' => 'required|date|after_or_equal:today',
        ];
    }
}
