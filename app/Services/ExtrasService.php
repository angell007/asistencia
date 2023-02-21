<?php

namespace App\Services;

use App\Clases\stdObject;
use App\Models\ConfiguracionEmpresa;
use App\Models\Funcionario;
use App\Models\TurnoRotativo;
use App\Traits\CalculateRotativoExtras;
use App\Traits\CalculateRotativoRecargos;
use App\Traits\Festivos;
use Carbon\Carbon;


class ExtrasService
{

    use Festivos;
    use CalculateRotativoExtras;
    use CalculateRotativoRecargos;

    public  $tiempoParaExtras;

    public function __construct(TranslateService $translateService)
    {
        $this->translateService = $translateService;
    }


    public function funcionarioFijo($filtroDiarioFecha)
    {


        $funcionario =  Funcionario::with(['diariosTurnoFijo' => $filtroDiarioFecha])
            ->with('turnoFijo.horariosTurnoFijo')
            ->find(request()->get('id'))->toArray();

        if (isset($funcionario['turno_fijo']['horarios_turno_fijo'])) {
            $funcionario['daysWork'] = $funcionario['turno_fijo']['horarios_turno_fijo'];
            foreach ($funcionario['diarios_turno_fijo']  as  $diario) {
                foreach ($funcionario['turno_fijo']['horarios_turno_fijo'] as $ky => $turno) {
                    if ($turno['dia'] == $this->translateService->translateDay(Carbon::create($diario['fecha'])->englishDayOfWeek)) {
                        array_push($funcionario['daysWork'][$ky], ['day' => $diario]);
                    }
                }
            }

            unset($funcionario['diarios_turno_fijo']);
        } else {
            return response()->json('Horario no asignado ');
        }

        return $funcionario;
    }

    public $q = "";

    public function funcionarioRotativo($filtroDiarioFecha)
    {

        $this->q = $filtroDiarioFecha;
        $funcionario =  Funcionario::with(['diariosTurnoRotativo' => $filtroDiarioFecha])
            ->with(['horariosTurnoRotativo' => $filtroDiarioFecha])
            ->find(request()->get('id'))->toArray();

        if (isset($funcionario['horarios_turno_rotativo'])) {

            $funcionario['daysWork'] = $funcionario['horarios_turno_rotativo'];

            foreach ($funcionario['diarios_turno_rotativo']  as  $diario) {
                foreach ($funcionario['horarios_turno_rotativo'] as $ky => $turno) {

                    if ($turno['fecha'] == $diario['fecha']) {
                        $diario['nombreDia'] = $this->translateService->translateDay(Carbon::create($diario['fecha'])->englishDayOfWeek);
                        $diario['turnoOficial'] = TurnoRotativo::find($turno['turno_rotativo_id'])->toArray();
                        array_push($funcionario['daysWork'][$ky], ['day' => $diario]);
                    }
                }
            }
            unset($funcionario['diarios_turno_rotativo']);
        } else {
            return false;
        }
        return $funcionario;
    }

    public function calcularExtras($funcionario)
    {
        //Tiempo Asignado
        $tiempoAsignado = ConfiguracionEmpresa::first()->toArray()['tiempo_trabajo'];

        foreach ($funcionario['daysWork'] as $ky => $day) {
            $descansa = false;
            switch ($descansa) {
                case true:
                    break;
                case false:
                    if (isset($day[0])) {

                        //Seteo valores
                        $toleranciaSalida = 0;
                        $laborado = $day[0]['day'];

                        $extras = new stdObject();
                        $extras->HorasExtrasNocturnas = 0;
                        $extras->HorasExtrasDiurnas = 0;
                        $extras->HorasExtrasNocturnasDominicales = 0;
                        $extras->HorasExtrasDiurnasDominicales = 0;
                        $extras->horasExtrasDominicalesFestivasNocturnas = 0;
                        $extras->horasExtrasDominicalesFestivasDiurnas = 0;


                        $recargos = new stdObject();
                        $recargos->horasRecargoDominicalNocturna = 0;
                        $recargos->horasRecargoNocturna = 0;
                        $recargos->horasRecargoFestivo = 0;
                        $recargos->horasRecargoDominicalDiurno = 0;

                        //Asistencia
                        [$asistencia, $salida, $fechaSalida] =   $this->Asistencia($laborado['fecha'], $laborado['hora_entrada_uno'], $laborado['hora_salida_uno']);

                        //Tiempo Laborado
                        $tiempoLaborado = 0;
                        $tiempoLaborado = $asistencia->diffInMinutes($salida, false);
                        $FinTurnoOficcial =  $asistencia->copy()->addMinutes($tiempoAsignado);
                        $FinTurnoReal =  $asistencia->copy()->addMinutes($tiempoLaborado);

                        //Se arma el objecto que sera enviado como parametro
                        $argumentos = new stdObject();
                        $argumentos->tiempoAsignado = $tiempoAsignado;
                        $argumentos->toleranciaSalida = $toleranciaSalida;
                        $argumentos->tiempoLaborado = $tiempoLaborado;
                        $argumentos->FinTurnoOficcial = $FinTurnoOficcial;
                        $argumentos->FinTurnoReal = $FinTurnoReal;
                        $argumentos->fechaSalida = $fechaSalida;
                        $argumentos->asistencia = $asistencia;

                        //Compruebo si trabajo mas del tiempo fijado y la tolerancia (Si trabajó extras)
                        if ($tiempoLaborado > ($tiempoAsignado + $toleranciaSalida)) {
                            $recargos = $this->getDataRecargo($argumentos);
                            $extras = $this->getDataExtra($argumentos);
                        }

                        if ($tiempoLaborado <= ($tiempoAsignado + $toleranciaSalida)) {
                            $recargos = $this->getDataRecargoSinExtras($argumentos);
                        }

                        $funcionario['daysWork'][$ky]['tiempoLaborado'] = round($tiempoLaborado / 60);
                        $funcionario['daysWork'][$ky]['tiempoAsignado'] = round($tiempoAsignado / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoDominicalNocturna'] = round($recargos->horasRecargoDominicalNocturna / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoNocturna'] = round($recargos->horasRecargoNocturna / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoFestivo'] = round($recargos->horasRecargoFestivo / 60);

                        $funcionario['daysWork'][$ky]['horasRecargoDominicalDiurno'] = round($recargos->horasRecargoDominicalDiurno / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasNocturnas'] = round($extras->HorasExtrasNocturnas / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasDiurnas'] = round($extras->HorasExtrasDiurnas / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasNocturnasDominicales'] = round($extras->HorasExtrasNocturnasDominicales / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasDiurnasDominicales'] = round($extras->HorasExtrasDiurnasDominicales / 60);

                        $funcionario['daysWork'][$ky]['horasExtrasDominicalesFestivasNocturnas'] = round($extras->horasExtrasDominicalesFestivasNocturnas / 60);
                        $funcionario['daysWork'][$ky]['horasExtrasDominicalesFestivasDiurnas'] = round($extras->horasExtrasDominicalesFestivasDiurnas / 60);
                    } else {

                        // No trabajó

                    }
                    break;
            }
        }
        return $funcionario;
    }


    // Asistencia
    public function Asistencia($fecha, $horaEntrada, $horaSalida)
    {

        $asistencia = Carbon::parse($fecha . $horaEntrada);
        $salida =  Carbon::parse($fecha . $horaSalida);
        $fechaSalida =  Carbon::parse($fecha .  $horaSalida);

        if (($horaSalida >=   '00:00:00' && $horaSalida <   $horaEntrada)) {
            $salida->addDay();
            $fechaSalida->addDay();
        }

        return [$asistencia, $salida,  $fechaSalida];
    }



    public function setTiempoExtra($cantidadEnMinutos)
    {
        $this->tiempoParaExtras - $cantidadEnMinutos;
    }
}
