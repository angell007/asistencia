<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeduccionRequestCreate;
use App\Models\Deduccion;
use App\Models\Funcionario;
use App\Models\ContableDeduccion;

class DeduccionesController extends Controller
{

    public function index()
    {
        return ContableDeduccion::all();
    }

    public function store(DeduccionRequestCreate $deduccionRequestCreate)
    {
        Deduccion::create(request()->all());
        return response()->json(['message' => 'Deducción creada correctamente']);
    }

    public function showFuncionario($id)
    {
        $funcionario = Funcionario::findOrFail($id);

        return Deduccion::where('funcionario_id', '=', $funcionario->id)->with('deduccion')->get();
    }

    public function destroy($id)
    {
        $deduccion = Deduccion::findOrFail($id);
        $deduccion->delete();

        return response()->json(['message' => 'Deducción eliminada correctamente']);
    }
}
