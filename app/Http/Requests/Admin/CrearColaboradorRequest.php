<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CrearColaboradorRequest extends FormRequest
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
            'nombres' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'],
            'apellido_paterno' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'],
            'apellido_materno' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'],
            'correo_personal' => ['required', 'email', 'max:255', 'unique:usuarios,correo_personal'],
            'correo' => ['required', 'string', 'max:255'],
            'clave' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que el correo corporativo completo sea único
            if ($this->filled('correo')) {
                /** @var \App\Models\Usuario $usuario */
                $usuario = Auth::user();
                if ($usuario) {
                    $empresaRepo = app(\App\Repositories\EmpresaRepositorio::class);
                    $empresa = $empresaRepo->findOneBy('usuario_id', $usuario->id);
                    if ($empresa) {
                        $correoCompleto = $this->correo . '@' . strtolower($empresa->nombre) . '.cx.com';
                        $existe = \App\Models\Usuario::where('correo', $correoCompleto)->exists();
                        if ($existe) {
                            $validator->errors()->add('correo', 'Este correo corporativo ya está en uso.');
                        }
                    }
                }
            }
        });
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

            'correo.required' => 'El correo corporativo es obligatorio.',
            'correo.string' => 'El correo corporativo debe ser una cadena de texto.',
            'correo.max' => 'El correo corporativo no puede superar los 255 caracteres.',

            'clave.required' => 'La contraseña es obligatoria.',
            'clave.string' => 'La contraseña debe ser una cadena de texto.',
            'clave.min' => 'La contraseña debe tener al menos 8 caracteres.',
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
    }
}
