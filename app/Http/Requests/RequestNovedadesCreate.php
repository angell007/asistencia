<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestNovedadesCreate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'funcionario_id' => 'required',
            'contable_licencia_incapacidad_id' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'tipo' => 'required',
            'modalidad' => 'required',
            'observacion' => '',
        ];
    }

    public function messages()
    {
        return [
            'funcionario_id.required' => 'El campo de funcionario es obligatorio',
            'contable_licencia_incapacidad_id.required' => 'El campo de novedad es obligatorio'
        ];
    }
}
