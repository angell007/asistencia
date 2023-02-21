<?php

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use App\Models\Funcionario;
use App\Models\ReporteExtras;
use App\Services\ExtrasService;
use Carbon\Carbon;

class HorasExtrasController extends Controller
{

    public function __construct(ExtrasService $extrasService)
    {
        $this->extrasService = $extrasService;
    }

    public function getDatosTurnoRotativo($fechaInicio, $fechaFin)
    {

        $callback3 =  function ($q)  use ($fechaInicio, $fechaFin) {
            $q->whereDate('fecha', '>=', $fechaInicio)
                ->whereDate('fecha_salida', '<=', $fechaFin);
        };

        $callback2 =  function ($q) use ($callback3, $fechaInicio, $fechaFin) {
            $q->where('tipo_turno', 'Rotativo')
                ->whereHas('diariosTurnoRotativo', $callback3)
                ->with('diariosTurnoRotativo', $callback3)
                ->whereHas('contractultimate', function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereDate('fecha_inicio', '<=', $fechaFin)
                        ->whereDate('fecha_fin', '>=', $fechaInicio);
                })
                ->select('funcionario.id', 'funcionario.dependencia_id', 'funcionario.nombres', 'funcionario.apellidos', 'funcionario.tipo_turno', 'funcionario.image');
        };


        return  CentroCosto::whereHas('dependencias.funcionarios', $callback2)
            ->with(['dependencias' => function ($q) use ($callback2) {
                $q->whereHas('funcionarios', $callback2)
                    ->with('funcionarios', $callback2);
            }])->get(['id', 'nombre']);
    }

    //Si la fecha de salida no esta definida no tomara el filtro
    public function getDatosTurnoFijo($fecha, $fechaInicio)
    {
        $callback = function ($query) use ($fecha, $fechaInicio) {
            $query->where('tipo_turno', 'Fijo')
                ->whereHas('contractultimate', function ($q) use ($fecha, $fechaInicio) {
                    $q->whereDate('fecha_inicio', '<=', $fecha)
                        ->whereDate('fecha_fin', '>=', $fechaInicio);
                })
                ->select('funcionario.id', 'funcionario.dependencia_id', 'funcionario.nombres', 'funcionario.apellidos', 'funcionario.tipo_turno', 'funcionario.image');
        };

        return  CentroCosto::whereHas('dependencias.funcionarios', $callback)
            ->with(['dependencias' => function ($q) use ($callback) {
                $q->whereHas('funcionarios', $callback)
                    ->with('funcionarios', $callback);
            }])->get(['id', 'nombre']);
    }


    public function store()
    {
        $atributos = request()->validate([
            'funcionario_id' => 'required',
            'fecha' => 'required',
            'ht' => 'required',
            'hed' => 'required',
            'hen' => 'required',
            'hedfd' => 'required',
            'hedfn' => 'required',
            'rn' => 'required',
            'rf' => 'required',
            'hed_reales' => 'required',
            'hen_reales' => 'required',
            'hedfd_reales' => 'required',
            'hedfn_reales' => 'required',
            'rn_reales' => 'required',
            'rf_reales' => 'required',
        ]);

        ReporteExtras::create($atributos);

        return response()->json(['message' => 'Horas extras validadas correctamente']);
    }

    /**
     * Se retorna un único dato debido a que una extra solo puede estar validada una única vez, es decir no pueden existir dos o más validaciones de una hora extra con el mismo día
     *
     * @param string $fecha
     * @return ReporteExtras
     */
    public function getDatosValidados($funcionario, $fecha)
    {
        $validada = ReporteExtras::where('fecha', $fecha)->where('funcionario_id', $funcionario)->orderBy('fecha')->first();
        if ($validada) {
            return $validada;
        }
    }

    public function update($id)
    {
        $validada = ReporteExtras::findOrFail($id);

        $atributos = request()->validate([
            'funcionario_id' => 'required',
            'fecha' => 'required',
            'ht' => 'required',
            'hed' => 'required',
            'hen' => 'required',
            'hedfd' => 'required',
            'hedfn' => 'required',
            'rn' => 'required',
            'rf' => 'required',
            'hed_reales' => 'required',
            'hen_reales' => 'required',
            'hedfd_reales' => 'required',
            'hedfn_reales' => 'required',
            'rn_reales' => 'required',
            'rf_reales' => 'required',
        ]);

        $validada->update($atributos);

        return response()->json(['Horas extras validadas y actualizadas correctamente']);
    }

    public function getInfoTotal()
    {
        $fechaInicio = request()->get('pd');
        $fechaFin = request()->get('ud');

        $filtroDiarioFecha = function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin])->orderBy('fecha');
        };

        switch (request()->get('tipo')) {
            case 'Fijo':
                $funcionario =  $this->extrasService->funcionarioFijo($filtroDiarioFecha);
                return $this->extrasService->calcularExtras($funcionario);
                break;
            case 'Rotativo':
                $funcionario =  $this->extrasService->funcionarioRotativo($filtroDiarioFecha);
                return $this->extrasService->calcularExtras($funcionario);
                break;
            default:
                return abort(404);
                break;
        }
    }
}
