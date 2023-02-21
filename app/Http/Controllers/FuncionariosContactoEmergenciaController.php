<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioContactoEmergencia;
use App\Models\Funcionario;

class FuncionariosContactoEmergenciaController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function getDatos()
    {
        return FuncionarioContactoEmergencia::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $attributos = request()->validate([
            'funcionario_id' => 'required',
            'parentesco' => 'required',
            'nombre_completo' => 'required',
            'telefono' => '',
            'celular' => '',
            'direccion' => ''
        ]);

        return response()->json(['message' => 'Contacto de emergencia creado correctamente', 'data' => FuncionarioContactoEmergencia::create($attributos)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $contactoId)
    {
        $funcionario = Funcionario::findOrFail($id);

        $contacto = FuncionarioContactoEmergencia::findOrFail($contactoId);

        $attributos = request()->validate([
            'funcionario_id' => 'required',
            'parentesco' => 'required',
            'nombre_completo' => 'required',
            'telefono' => '',
            'celular' => '',
            'direccion' => ''
        ]);

        $contacto->update($attributos);
        return response()->json(['message' => 'Contacto de emergencia acutalizado correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
