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
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'ruc' => ['required', 'string', 'digits:11', 'unique:empresas,ruc'],
            'telefono' => ['required', 'string', 'max:20'],
            'plan' => ['required', 'exists:plan_servicios,id'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',

            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.digits' => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique' => 'Este RUC ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',

            'plan.required' => 'Debe seleccionar un plan.',
            'plan.exists' => 'El plan seleccionado no es válido.',

            'terms.accepted' => 'Debe aceptar los términos y condiciones.',
        ];
    }
}
