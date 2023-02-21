<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagosNominaStoreRequest extends FormRequest
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
            'admin_id' => 'required',
            'inicio_periodo' => 'required|string',
            'fin_periodo' => 'required|string',
            'total_salarios' => 'required|numeric',
            'total_retenciones' => 'required|numeric',
            'total_provisiones' => 'required|numeric',
            'total_seguridad_social' => 'required|numeric',
            'total_parafiscales' => 'required|numeric',
            'total_extras_recargos' => 'required|numeric',
            'total_ingresos' => 'required|numeric',
            'costo_total' => 'required|numeric'
        ];
    }
}
