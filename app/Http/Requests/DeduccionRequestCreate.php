<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeduccionRequestCreate extends FormRequest
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
            'contable_deduccion_id' => 'required',
            'valor' => 'required|gt:0',
        ];
    }
}
