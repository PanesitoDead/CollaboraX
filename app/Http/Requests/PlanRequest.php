<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Puedes agregar lógica de autorización aquí si es necesario
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
            'beneficios' => 'required|string',
            'costo_soles' => 'required|numeric|min:0',
            'cant_usuarios' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del plan es obligatorio.',
            'nombre.string' => 'El nombre del plan debe ser una cadena de texto.',
            'nombre.max' => 'El nombre del plan no puede superar los 255 caracteres.',
            'beneficios.required' => 'Los beneficios del plan son obligatorios.',
            'beneficios.string' => 'Los beneficios deben ser una cadena de texto.',
            'costo_soles.required' => 'El costo en soles es obligatorio.',
            'costo_soles.numeric' => 'El costo debe ser un número.',
            'costo_soles.min' => 'El costo no puede ser menor a 0.',
            'cant_usuarios.required' => 'La cantidad de usuarios es obligatoria.',
            'cant_usuarios.integer' => 'La cantidad de usuarios debe ser un número entero.',
            'cant_usuarios.min' => 'La cantidad de usuarios debe ser mayor a 0.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede superar los 500 caracteres.'
        ];
    }
}
