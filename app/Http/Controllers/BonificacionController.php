<?php

namespace App\Http\Controllers;

use App\Http\Resources\BonificacionResource;
use App\Models\Bonificacion;
use App\Models\ContableIngreso;
use Illuminate\Http\Request;


class BonificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new BonificacionResource(Bonificacion::all());
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
        return new BonificacionResource(Bonificacion::create((array)request()->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bonificacion  $bonificacion
     * @return \Illuminate\Http\Response
     */
    public function show(Bonificacion $bonificacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bonificacion  $bonificacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Bonificacion $bonificacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bonificacion  $bonificacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bonificacion $bonificacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bonificacion  $bonificacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Bonificacion::find($id)->delete();
    }
}
