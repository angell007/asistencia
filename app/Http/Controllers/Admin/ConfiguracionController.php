<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PorcentageIncapacidad;

class ConfiguracionController extends Controller
{
    public function getPorcentajesIncapacidades()
    {
        return response()->json(PorcentageIncapacidad::get(['valor', 'nombre', 'id']), 200);
    }
}
