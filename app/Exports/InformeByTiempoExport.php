<?php

namespace App\Exports;

use App\Indicador;
use App\Models\Funcionario;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InformeByTiempoExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $fechaInicio = null;
    public  $fechaFin = null;
    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function view(): View
    {
        $funcionarios =  Funcionario::with('contractultimate')->from('funcionario as func')
            ->join('llegada_tarde as llt', 'llt.funcionario_id', 'func.id')
            ->select(
                'func.id',
                'func.nombres',
                'func.apellidos',
                'func.image',
                'func.dependencia_id',
                DB::raw('sum((time_to_sec(llt.entrada_real) - time_to_sec(entrada_turno))/60) as tiempo_acumulado')
            )
            ->whereBetween('llt.fecha', [$this->fechaInicio, $this->fechaFin])
            ->groupBy('func.id')
            ->orderBy('tiempo_acumulado', 'desc')
            ->take(5)
            ->with(['dependencia' => function ($query) {
                $query->select('id', 'nombre', 'centro_costo_id')->with('centroCosto:id,nombre');
            }])->get();
        return view('informesExcel.byTiempo', compact('funcionarios'));
    }
}
