<?php

namespace App\Exports;

use App\Models\Funcionario;
use App\Models\LlegadaTarde;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class LLegadasTarde implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $fechaInicio = null;
    public  $fechaFin = null;
    public function __construct($fechaInicio,   $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function view(): View
    {
        try {

            $llegadasTarde = LlegadaTarde::with('funcionario.dependencia.centroCosto')
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy(
                    'fecha'
                )->get(['id', 'funcionario_id', 'fecha', 'entrada_turno', 'entrada_real']);

            return view('informesExcel.llegadasTarde', compact('llegadasTarde'));
        } catch (\Exception $th) {
            dd($th->getMessage());
        }
    }
}
