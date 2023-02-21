<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcionario;
use App\Models\FuncionarioDocumento;
use Illuminate\Support\Facades\Storage;

class FuncionariosDocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function store($identidad)
    {
        $funcionario = Funcionario::where('id', $identidad)->firstOrFail();

        if (request('file')->isvalid()) {

            $documento = request('file');
            $file = $documento->getClientOriginalName();
            $img = request()->file('file')->store("funcionarios/$funcionario->id/documentos", 's3', 'public');
            $fully =  Storage::disk('s3')->url($img);
        } else {
            return response()->json(['message' => 'Falta documento'], 400);
        }

        $funcionarioDocumento = new FuncionarioDocumento();
        $funcionarioDocumento->nombre =  pathinfo($file, PATHINFO_FILENAME);
        $funcionarioDocumento->extension =  pathinfo($file, PATHINFO_EXTENSION);
        $funcionarioDocumento->path =  $fully;
        $funcionarioDocumento->funcionario_id = $funcionario->id;
        $funcionarioDocumento->save();

        return response()->json(['message' => 'Documento subido y creado correctamente'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFiles($id)
    {
        return   FuncionarioDocumento::where('funcionario_id', $id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteFiles($id)
    {
        try {
            $file =   FuncionarioDocumento::findOrFail($id);
            if (Storage::disk('s3')->exists($file->path)) {
                Storage::disk('s3')->delete($file->path);
            }
            $file->delete();
            return response()->json('Operacion realizada correctamente', 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 400);
        }
    }
}
