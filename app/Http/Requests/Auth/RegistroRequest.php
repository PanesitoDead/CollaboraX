<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegistroRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'email' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9._%+-]+$/'],
            'email_personal' => ['nullable', 'string', 'email', 'max:255'],
            'nombre' => ['required', 'string', 'max:255','regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'ruc' => ['required', 'string', 'digits:11', 'unique:empresas,ruc'],
            'telefono' => ['required', 'digits:9'],
            'plan' => ['required', 'exists:plan_servicios,id'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string'   => 'El correo electrónico debe ser una cadena de texto.',
            'email.max'      => 'El correo electrónico no puede superar los 64 caracteres.',
            'email.regex'    => 'El correo electrónico solo puede contener letras, números, puntos, guiones bajos y signos más.',
            
            'email_personal.email' => 'El correo electrónico personal debe ser una dirección de correo válida.',
            'email_personal.max' => 'El correo electrónico personal no puede superar los 255 caracteres.',

            'descripcion.max' => 'La descripción no puede superar los 1000 caracteres.',

            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'La confirmación de contraseña no coincide.',

            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar los 255 caracteres.',
            'nombre.regex'    => 'El nombre solo puede contener letras y espacios.',

            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.digits'   => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique'   => 'Este RUC ya está registrado.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits'   => 'El teléfono debe contener exactamente 9 dígitos y no puede tener letras.',

            'plan.required' => 'Debe seleccionar un plan.',
            'plan.exists'   => 'El plan seleccionado no es válido.',

            'terms.accepted' => 'Debe aceptar los términos y condiciones.',
        ];
    }
}
