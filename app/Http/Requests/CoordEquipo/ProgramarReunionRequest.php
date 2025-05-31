<?php

namespace App\Http\Requests\CoordEquipo;

use Illuminate\Foundation\Http\FormRequest;

class ProgramarReunionRequest extends FormRequest
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
            'titulo' => 'required|string|max:255',
            'fecha' => 'required|date|after_or_equal:today',
            'duracion' => 'required|integer|in:30,60,90,120',
            'modalidad_id' => 'required|exists:modalidades,id',
            'descripcion' => 'nullable|string|max:1000',
            'sala' => 'nullable|string|max:255',
        ];
    }

}
