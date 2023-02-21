<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Funcionario;
use App\Models\Renovacion;
use App\Traits\RenovacionContrato;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RenovacionController extends Controller
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

    public function renovarContrato()
    {

        $mesesAnio = 12;
        $vecesRenovadas = 3;

        try {
            $funcionarioContract = Funcionario::with('contractultimate')->find(request()->get('id'));

            $periodo =  ceil(Carbon::parse($funcionarioContract->contractultimate->fecha_inicio)
                ->floatDiffInMonths(Carbon::parse($funcionarioContract->contractultimate->fecha_fin)));

            $newDate =  Carbon::parse($funcionarioContract->contractultimate->fecha_fin)->addMonths($periodo)->format('Y-m-d');

            if (Contrato::withCount('renovaciones')->find($funcionarioContract->contractultimate->id)->renovaciones_count > $vecesRenovadas) {
                $meses =  $mesesAnio - $periodo;
                $newDate = Carbon::parse($newDate)->addMonths($meses)->format('Y-m-d');
                $periodo = $mesesAnio;
            }

            $updateContract = $funcionarioContract->contractultimate->update([
                'fecha_fin' =>  $newDate
            ]);

            if ($updateContract) {
                Renovacion::create([
                    'contrato_id' => $funcionarioContract->contractultimate->id,
                    'periodo' => $periodo,
                ]);

                return response()->json('Renovacion exitosa');
            }
        } catch (\Exception $th) {
            dump($th);
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Renovacion  $renovacion
     * @return \Illuminate\Http\Response
     */
    public function show(Renovacion $renovacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Renovacion  $renovacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Renovacion $renovacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Renovacion  $renovacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Renovacion $renovacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Renovacion  $renovacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Renovacion $renovacion)
    {
        //
    }
}
