<?php

namespace App\Http\Requests\CoordEquipo;

use Hash;
use Illuminate\Foundation\Http\FormRequest;

class ActualizarClaveRequest extends FormRequest
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
    public function rules()
    {
        return [
            'password_actual' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Hash::check($this->password_actual, auth()->user()->clave)) {
                $validator->errors()->add('password_actual', 'La contraseña actual no es correcta.');
            }
        });
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'La nueva contraseña no coincide con la confirmación.',
            'password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
            'password_actual.required' => 'Debes ingresar tu contraseña actual.',
        ];
    }
}
