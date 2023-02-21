<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpresaUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'razon_social' => 'required|min:3',
            'tipo_documento' => 'required',
            'numero_documento' => 'required',
            'fecha_constitucion' => 'required',

            'email_contacto' => [
                'required',
                Rule::unique('empresa', 'email_contacto,')->ignore($this->empresa)
            ],
            
            'telefono_contacto' => 'required',
            'max_horas_extras' => 'required',
            'max_festivos_legales' => 'required',
            'max_llegadas_tarde' => 'required',
            'salario_base' => 'required',
            'auxilio_transporte' => 'required',
            'hora_inicio_noche' => 'required',
            'hora_fin_noche' => 'required',
            'festivos' => '',
            'frecuencia_pago' => 'required',
            'medio_pago' => 'required',
            'banco_id' => 'required',
            'tipo_cuenta' => 'required',
            'numero_cuenta' => 'required|min:10|max:20',
            'operador_pago' => 'required',
            'arl_id' => 'required',
            'ley_1429' => 'required',
            'ley_590' => 'required',
            'ley_1607' => 'required',
            'digito_verificacion' => 'required_if:tipo_documento,NIT'
        ];
    }
}
