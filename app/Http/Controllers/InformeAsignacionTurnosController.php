<?php

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use App\Models\TurnoRotativo;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class InformeAsignacionTurnosController extends Controller
{

    protected $diaInicialSemana;
    protected $diaFinalSemana;
    protected $centroCosto;
    protected $funcionariosToPrint;
    protected $infoCentroCosto;
    protected $dependencias;

    public function getInfoCentroCosto()
    {
        $centros =  CentroCosto::whereHas('dependencias.funcionarios.horariosTurnoRotativo', function ($query) {
            $query->where('tipo_turno', 'Rotativo');
        })->with(['dependencias' => function ($query) {
            $query->whereHas('funcionarios', function ($query) {
                $query->where('tipo_turno', 'Rotativo');
            })->with(['funcionarios' => function ($query) {
                $query->select('id', 'dependencia_id', 'nombres', 'apellidos', 'image')->where('tipo_turno', 'Rotativo')
                    ->with(['horariosTurnoRotativo' =>  function ($q) {
                        $q->with('turnoRotativo')
                            ->whereBetween('fecha', [Carbon::parse($this->diaInicialSemana), Carbon::parse($this->diaFinalSemana)]);
                    }]);
            }]);
        }])->where('id', $this->centroCosto)->get();

        $this->infoCentroCosto = $centros[0];

        $this->getFuncionarios($centros);
    }

    public function getFuncionarios(Collection $centros)
    {
        collect($centros)->each(function ($centro) {
            $centro->dependencias->each(function ($dependencia) {
                $this->dependencias->push($dependencia);
                $dependencia->funcionarios->each(function ($funcionario) {
                    $this->funcionariosToPrint->push($funcionario);
                });
            });
        });
    }


    public function generatePdf()
    {
        $this->funcionariosToPrint = collect();
        $this->dependencias = collect();
        $this->diaInicialSemana = request()->get('diaInicialSemana');
        $this->diaFinalSemana = request()->get('diaFinalSemana');
        $this->centroCosto = request()->get('centro');

        $inicio = Carbon::parse($this->diaInicialSemana);
        $fin = Carbon::parse($this->diaFinalSemana);

        $this->getInfoCentroCosto();

        return PDF::loadView(
            'pdfs.asignacion',
            [
                'funcionarios' => $this->funcionariosToPrint,
                'inicio' =>  $inicio,
                'fin' => $fin,
                'infoCentroCosto' => $this->infoCentroCosto,
                'dependencias' => $this->dependencias,
            ]
        )->download('Turnos_rotativos');
    }
}
