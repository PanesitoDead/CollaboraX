<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditarColaboradorRequest extends FormRequest
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
        $colaboradorId = $this->route('id'); // ID del colaborador que se está editando
        
        return [
            'nombres' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'],
            'apellido_paterno' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'],
            'apellido_materno' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'],
            'correo_personal' => ['required', 'email', 'max:255', 'unique:usuarios,correo_personal,' . $colaboradorId . ',id'],
            'doc_identidad' => ['nullable', 'string', 'max:8', 'regex:/^[0-9]+$/'],
            'telefono' => ['nullable', 'string', 'digits:9'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB max
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.string' => 'El nombre debe ser una cadena de texto.',
            'nombres.max' => 'El nombre no puede superar los 255 caracteres.',
            'nombres.regex' => 'El nombre solo puede contener letras y espacios.',

            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.string' => 'El apellido paterno debe ser una cadena de texto.',
            'apellido_paterno.max' => 'El apellido paterno no puede superar los 255 caracteres.',
            'apellido_paterno.regex' => 'El apellido paterno solo puede contener letras y espacios.',

            'apellido_materno.required' => 'El apellido materno es obligatorio.',
            'apellido_materno.string' => 'El apellido materno debe ser una cadena de texto.',
            'apellido_materno.max' => 'El apellido materno no puede superar los 255 caracteres.',
            'apellido_materno.regex' => 'El apellido materno solo puede contener letras y espacios.',

            'correo_personal.required' => 'El correo personal es obligatorio.',
            'correo_personal.email' => 'El correo personal debe ser una dirección de correo válida.',
            'correo_personal.max' => 'El correo personal no puede superar los 255 caracteres.',
            'correo_personal.unique' => 'Este correo personal ya está registrado.',

            'doc_identidad.max' => 'El documento de identidad no puede superar los 8 caracteres.',
            'doc_identidad.regex' => 'El documento de identidad solo puede contener números.',

            'telefono.digits' => 'El teléfono debe contener exactamente 9 dígitos.',

            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',

            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'avatar.max' => 'La imagen no puede superar los 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Limpiar espacios en blanco de los campos de texto
        if ($this->has('nombres')) {
            $this->merge(['nombres' => trim($this->nombres)]);
        }
        if ($this->has('apellido_paterno')) {
            $this->merge(['apellido_paterno' => trim($this->apellido_paterno)]);
        }
        if ($this->has('apellido_materno')) {
            $this->merge(['apellido_materno' => trim($this->apellido_materno)]);
        }
        if ($this->has('correo_personal')) {
            $this->merge(['correo_personal' => trim(strtolower($this->correo_personal))]);
        }
        if ($this->has('doc_identidad')) {
            $this->merge(['doc_identidad' => trim($this->doc_identidad)]);
        }
        if ($this->has('telefono')) {
            $this->merge(['telefono' => preg_replace('/[^0-9]/', '', $this->telefono)]);
        }
    }
}
