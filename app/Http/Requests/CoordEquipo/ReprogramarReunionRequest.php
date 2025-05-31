<?php

namespace App\Http\Requests\CoordEquipo;

use Illuminate\Foundation\Http\FormRequest;

class ReprogramarReunionRequest extends FormRequest
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
            'nueva_fecha' => 'required|date|after_or_equal:today',
            'nueva_hora' => 'required|date_format:H:i',
            'motivo' => 'nullable|string|max:1000',
        ];
    }
}
