<?php

namespace App\Http\Requests\CoordEquipo;

use Illuminate\Foundation\Http\FormRequest;

class InvitarColaboradoresRequest extends FormRequest
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
            'colaboradores' => 'required|array|min:1',
            'colaboradores.*' => 'exists:trabajadores,id',
        ];
    }

    public function messages()
    {
        return [
            'colaboradores.required' => 'Debes seleccionar al menos un colaborador.',
            'colaboradores.*.exists' => 'Uno de los colaboradores seleccionados no es válido.',
        ];
    }
}
