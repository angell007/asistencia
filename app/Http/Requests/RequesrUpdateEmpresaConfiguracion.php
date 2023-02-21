<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequesrUpdateEmpresaConfiguracion extends FormRequest
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
            'empresa_id' => 'required',
            'calculo_incapacidad_general' => 'required',
            'pago_deducciones' => 'required',
            'pago_recurrente' => 'required',
            'pago_subsidio_transporte' => 'required',
            'afecta_subsidio_transporte' => 'required',
            'pagar_vacaciones_31' => 'required',
        ];
    }
}
