<?php

namespace App\Http\Controllers;

use App\Models\Formato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormatoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->success(Formato::all());
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
    public function store(Request $request)
    {
        $fully =  null;

        if (request()->hasFile('firma')) {
            $img = request()->file('firma')->store('firmas', 's3', 'public');
            $fully =  Storage::disk('s3')->url($img);
        }

        return response()->json(Formato::create(
            [
                'logo' =>  $fully,
                'tipo_formato_id' => 1,
                'firma' =>  $fully,
                'encabezado' => request()->get('encabezado'),
                'cuerpo' => preg_replace("/&nbsp;/", ' ', request()->get('content')),
                'informacion' => request()->get('informacion'),
                'responsable' => request()->get('responsable')
            ]
        ), 200);
        return response()->json(request()->all(), 400);


        // Formato::create(request()->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Formato  $formato
     * @return \Illuminate\Http\Response
     */
    public function show(Formato $formato)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Formato  $formato
     * @return \Illuminate\Http\Response
     */
    public function edit(Formato $formato)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Formato  $formato
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Formato $formato)
    {
        return response($formato);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Formato  $formato
     * @return \Illuminate\Http\Response
     */
    public function destroy(Formato $formato)
    {
        //
    }
}
