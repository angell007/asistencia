<?php


class xxx
{

    public  $festivos;
    public  $tiempoParaExtras;

    public function __construct(TranslateService $translateService)
    {
        $this->translateService = $translateService;
        $this->festivos = collect(['01/01/2020', '06/01/2020', '23/03/2020', '09/04/2020', '10/04/2020', '01/05/2020', '25/05/2020', '15/06/2020', '22/06/2020', '29/06/2020', '20/07/2020', '07/08/2020', '17/08/2020', '12/10/2020', '02/11/2020', '16/11/2020', '08/12/2020', '25/12/2020', '01/01/2021', '11/01/2021', '22/03/2021']);
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

    public function funcionarioRotativo($filtroDiarioFecha)
    {
        $funcionario =  Funcionario::with(['diariosTurnoRotativo' => $filtroDiarioFecha])
            ->with(['horariosTurnoRotativo'  => $filtroDiarioFecha])
            ->find(request()->get('id'))->toArray();

        if (isset($funcionario['horarios_turno_rotativo'])) {

            $funcionario['daysWork'] = $funcionario['horarios_turno_rotativo'];

            foreach ($funcionario['diarios_turno_rotativo']  as  $diario) {
                foreach ($funcionario['horarios_turno_rotativo'] as $ky => $turno) {

                    if ($turno['fecha'] == $diario['fecha']) {
                        array_push($funcionario['daysWork'][$ky], ['day' => $diario]);
                    }
                }
            }
            unset($funcionario['diarios_turno_rotativo']);
        } else {
            return response()->json('Horario no asignado ');
        }
        return $funcionario;
    }

    public function calcularExtras($funcionario)
    {

        foreach ($funcionario['daysWork'] as $ky => $day) {
            $descansa = false;
            switch ($descansa) {
                case true:
                    break;
                case false:
                    if (isset($day[0])) {

                        //Seteo valores

                        $toleranciaSalida = 0;
                        // $cotizaExtras = 0;
                        $rounTwoLaborado = false;
                        $this->tiempoParaExtras = 0;
                        $horasRecargoDominicalNocturna = 0;
                        $horasRecargoNocturna = 0;
                        $horasRecargoDominicalDiurno = 0;
                        $HorasExtrasDiurnas = 0;
                        $HorasExtrasNocturnas = 0;
                        $HorasExtrasNocturnasDominicales = 0;
                        $HorasExtrasDiurnasDominicales = 0;

                        $laborado = $day[0]['day'];
                        $this->tiempoParaExtras = 0;

                        $funcionario['daysWork'][$ky]['tiempoLaborado'] = 0;
                        $funcionario['daysWork'][$ky]['tiempoAsignado'] = 0;
                        $funcionario['daysWork'][$ky]['horasRecargoDominicalNocturna'] = 0;
                        $funcionario['daysWork'][$ky]['horasRecargoNocturna'] = 0;
                        $funcionario['daysWork'][$ky]['horasRecargoDominicalDiurno'] = 0;
                        $funcionario['daysWork'][$ky]['horasRecargoDiurno'] = 0;
                        $funcionario['daysWork'][$ky]['HorasExtrasNocturnas'] = 0;

                        dd($laborado);

                        //Tiempo Asignado

                        $tiempoAsignado = 480;
                        // $tiempoAsignado = $this->tiempoAsignado($day['hora_inicio_uno'], $day['hora_fin_uno'], $day['hora_inicio_dos'],  $day['hora_fin_dos']);

                        //Asistencia
                        [$asistenciaUno, $salidaUno, $asistenciaDos,  $salidaDos,  $fechaSalida] =   $this->Asistencia($laborado['fecha'], $laborado['hora_entrada_uno'], $laborado['hora_salida_uno'], $laborado['hora_entrada_dos'], $laborado['hora_salida_dos']);

                        //Tiempo Laborado
                        $rounOne = $asistenciaUno->diffInMinutes($salidaUno, false);

                        $tiempoLaborado = 0;
                        // Compruebo que haya entrado en el segundo turno
                        if ($laborado['hora_entrada_dos']  != 'null') {
                            $rounTwoLaborado = true;
                            $rounTwo = $asistenciaDos->diffInMinutes($salidaDos, false);
                            $tiempoLaborado = $rounOne + $rounTwo;
                        } else {
                            $salidaDos =  $salidaUno;
                            $rounOne = $asistenciaUno->diffInMinutes($salidaUno, false);
                            $tiempoLaborado  = $rounOne;
                        }

                        //Asigno el final de turno si trabajo o no el segundo turno 
                        switch ($rounTwoLaborado) {
                                //Hora a la que debió salir 
                            case true:
                                $FinTurno = $asistenciaDos->addMinutes((8 * 60) - $asistenciaUno->diffInMinutes($salidaUno));
                                break;
                            default:
                                $FinTurno =  $asistenciaUno->addMinutes($tiempoAsignado);
                                break;
                        }

                        //Compruebo si trabajo mas del tiempo fijado y la tolerancia (Si trabajó extras)
                        if ($tiempoLaborado > ($tiempoAsignado + $toleranciaSalida)) {

                            //Obtengo El tiempo que sera extra
                            $this->tiempoParaExtras =  abs($tiempoAsignado - $tiempoLaborado);

                            /************************************************************************Recargos cuando se tiene horas extas  *********************************************************************************/
                            if ($FinTurno->hour > 21 || $FinTurno->hour == 00) {
                                if ($this->translateService->translateDay($fechaSalida->englishDayOfWeek == 'Sunday')) {
                                    $horasRecargoDominicalNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos->addDay());
                                }
                                if ($FinTurno->hour == 00) {
                                    $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos->addDay());
                                } else {
                                    $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos);
                                }
                            }

                            if ($FinTurno->hour > 00 && $FinTurno->hour <= 06) {
                                if ($this->translateService->translateDay($fechaSalida->englishDayOfWeek == 'Sunday')) {
                                    $horasRecargoDominicalNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '00:00:00')->diffInMinutes($salidaDos);
                                }

                                if ($asistenciaUno->hour <= 21) {
                                    $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') .  '21:00:00')->diffInMinutes($salidaDos->addDay()->format('Y-m-d' . '00:00.00'));
                                } else {
                                    $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '00:00:00')->diffInMinutes($salidaDos);
                                }
                            }
                            /***********************************************************************************************************************************************************************************************/


                            //  Compruebo si Solo se le pagan horas nopturnas
                            if ($FinTurno->hour > 21 ||  ($FinTurno->hour > 00 &&  $FinTurno->hour <= 06)) {
                                $HorasExtrasNocturnas += $this->tiempoParaExtras;
                                $this->setTiempoExtra($this->tiempoParaExtras);
                            }

                            // Compruebo si Solo se le pagan horas extras diurnas
                            if ($FinTurno->hour <= 21 && $FinTurno->hour >  06) {

                                // verifico que no me tome el siguiente dia si el turno temina  a las 00
                                if ($FinTurno->diffInMinutes($salidaDos->format('Y-m-d')  . '21:00:00') / 60 >= 24) {
                                    $tiempoParaInicioNocturno = $FinTurno->diffInMinutes($salidaDos->subDay()->format('Y-m-d')  . '21:00:00');
                                } else {
                                    $tiempoParaInicioNocturno = $FinTurno->diffInMinutes($salidaDos->format('Y-m-d')  . '21:00:00');
                                }
                                // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                                if ($this->tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                                    // Asigno todo el tiempo Extra como diurno


                                    $HorasExtrasDiurnas = $this->tiempoParaExtras;
                                    $FinTurno->addMinutes($HorasExtrasDiurnas);
                                    $this->setTiempoExtra($HorasExtrasDiurnas);
                                } else {
                                    // Asigno parte del tiempo a extras diurnas
                                    $HorasExtrasDiurnas = $tiempoParaInicioNocturno;
                                    $FinTurno->addMinutes($HorasExtrasDiurnas);

                                    //Obtengo el resto de horas que sumaran como nocturnas
                                    $this->tiempoParaExtras = $this->tiempoParaExtras - $HorasExtrasDiurnas;

                                    if ($asistenciaUno->format('Y-m-d') != $salidaDos->format('Y-m-d')) {
                                        $tiempoParaInicioSiguienteDia = $FinTurno->diffInMinutes(Carbon::parse($salidaDos->format('Y-m-d') . '00:00:00'));
                                    } else {
                                        $tiempoParaInicioSiguienteDia = $FinTurno->diffInMinutes(Carbon::parse($salidaDos->format('Y-m-d') . '00:00:00')->addDay());
                                    }

                                    if ($this->tiempoParaExtras - $tiempoParaInicioSiguienteDia < 0) {
                                        // Asigno todo el tiempo extra nocturno el mismo dia
                                        $HorasExtrasNocturnas += $this->tiempoParaExtras;
                                        $FinTurno->addMinutes($HorasExtrasNocturnas);
                                        $this->setTiempoExtra($this->tiempoParaExtras);
                                    } else {
                                        // Asigno parte del tiempo extra nocturno el mismo dia
                                        $HorasExtrasNocturnas += $tiempoParaInicioSiguienteDia;
                                        $FinTurno->addMinutes($HorasExtrasNocturnas);
                                        $this->tiempoParaExtras = $this->tiempoParaExtras - $tiempoParaInicioSiguienteDia;
                                        $tiempoParaInicioNuevoDiurno = $FinTurno->diffInMinutes(Carbon::parse($salidaDos->format('Y-m-d') . '06:00:00'));

                                        if ($this->translateService->translateDay(Carbon::parse($FinTurno)->englishDayOfWeek) == 'Domingo' ||  $this->festivos->contains(Carbon::parse($FinTurno)->format('Y-m-d'))) {
                                            if ($this->tiempoParaExtras - $tiempoParaInicioNuevoDiurno < 0) {
                                                // Asigno todo el tiempo extra nocturno siguiente dia
                                                $HorasExtrasNocturnasDominicales = $this->tiempoParaExtras;
                                                $FinTurno->addMinutes($HorasExtrasNocturnasDominicales);
                                                $this->setTiempoExtra($this->tiempoParaExtras);
                                            } else {
                                                // Asigno parte del tiempo extra nocturno siguiente dia
                                                $HorasExtrasNocturnasDominicales = $tiempoParaInicioSiguienteDia;
                                                $FinTurno->addMinutes($HorasExtrasNocturnasDominicales);
                                                $this->tiempoParaExtras = $this->tiempoParaExtras - $tiempoParaInicioSiguienteDia;

                                                //Horas Diurnas Siguiente Dia
                                                $tiempoParaInicioNocturno = $FinTurno->diffInMinutes($salidaDos->format('Y-m-d') . '21:00:00');
                                                // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                                                if ($this->tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                                                    // Asigno todo el tiempo Extra como diurno
                                                    $HorasExtrasDiurnasDominicales =  $HorasExtrasDiurnasDominicales + $this->tiempoParaExtras;
                                                    $FinTurno->addMinutes($HorasExtrasDiurnasDominicales);
                                                    $this->setTiempoExtra($this->tiempoParaExtras);
                                                }
                                            }
                                        }
                                        if ($this->tiempoParaExtras - $tiempoParaInicioNuevoDiurno < 0) {
                                            // Asigno todo el tiempo extra nocturno siguiente dia
                                            $HorasExtrasNocturnas += $this->tiempoParaExtras;
                                            $FinTurno->addMinutes($HorasExtrasNocturnas);
                                            $this->setTiempoExtra($this->tiempoParaExtras);
                                        } else {
                                            // Asigno parte del tiempo extra nocturno siguiente dia
                                            $HorasExtrasNocturnas = $tiempoParaInicioSiguienteDia;
                                            $FinTurno->addMinutes($HorasExtrasNocturnas);
                                            $this->tiempoParaExtras = $this->tiempoParaExtras - $tiempoParaInicioSiguienteDia;
                                            //Horas Diurnas Siguiente Dia
                                            $tiempoParaInicioNocturno = $FinTurno->diffInMinutes($salidaDos->format('Y-m-d') . '21:00:00', false);
                                            // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                                            if ($this->tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                                                // Asigno todo el tiempo Extra como diurno
                                                $HorasExtrasDiurnas =  $HorasExtrasDiurnas + $this->tiempoParaExtras;
                                                $FinTurno->addMinutes($HorasExtrasDiurnas);
                                                $this->setTiempoExtra($this->tiempoParaExtras);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {

                            /*******************************************************************Calculo recargos cuando no hay Extras*****************************************************************/

                            if ($salidaDos->hour > 21 || $salidaDos->hour == 00) {
                                if ($this->translateService->translateDay($fechaSalida->englishDayOfWeek == 'Sunday')) {
                                    $horasRecargoDominicalNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos->addDay());
                                }
                                if ($salidaDos->hour == 00) {
                                    $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos->addDay());
                                } else {
                                    $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos);
                                }
                            }

                            if ($salidaDos->hour > 00 && $salidaDos->hour <= 6) {
                                if ($this->translateService->translateDay($fechaSalida->englishDayOfWeek == 'Sunday')) {
                                    $horasRecargoDominicalNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '00:00:00')->diffInMinutes($salidaDos);
                                }

                                $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') . '00:00:00')->diffInMinutes($salidaDos);

                                if ($asistenciaUno->hour <= 21) {
                                    $horasRecargoNocturna += Carbon::parse($fechaSalida->format('Y-m-d') .  '21:00:00')->diffInMinutes($salidaDos->addDay()->format('Y-m-d' . '00:00.00'));
                                }
                            }

                            if ($salidaDos->hour > 06 && $salidaDos->hour < 21) {
                                if ($this->translateService->translateDay($fechaSalida->englishDayOfWeek == 'Sunday')) {
                                    $horasRecargoDominicalDiurno += Carbon::parse($fechaSalida->format('Y-m-d') . '06:00:00')->diffInMinutes($salidaDos);
                                    if ($asistenciaUno->hour <= 21) {
                                        $horasRecargoDominicalDiurno += Carbon::parse($fechaSalida->format('Y-m-d') .  '21:00:00')->diffInMinutes($salidaDos->addDay()->format('Y-m-d' . '00:00.00'));
                                    }
                                }
                            }
                        }

                        /***************************************************************************************************************************************************************************************/


                        $funcionario['daysWork'][$ky]['tiempoLaborado'] = round($tiempoLaborado / 60);
                        $funcionario['daysWork'][$ky]['tiempoAsignado'] = round($tiempoAsignado / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoDominicalNocturna'] = round($horasRecargoDominicalNocturna / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoNocturna'] = round($horasRecargoNocturna / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoDominicalDiurno'] = round($horasRecargoDominicalDiurno / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasNocturnas'] = round($HorasExtrasNocturnas / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasDiurnas'] = round($HorasExtrasDiurnas / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasNocturnasDominicales'] = round($HorasExtrasNocturnasDominicales / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasDiurnasDominicales'] = round($HorasExtrasDiurnasDominicales / 60);

                        dd($funcionario['daysWork']);
                    } else {
                        // No trabajó
                    }
                    break;
            }
        }
        return $funcionario;
    }

    // Tiempo asignado en el turno
    public function tiempoAsignado($horaInicioUno, $horaFinUno, $horaInicioDos, $horaFinDos)
    {
        $rounOneFijado = Carbon::parse($horaInicioUno)->diffInMinutes(Carbon::parse($horaFinUno), false);
        if (Carbon::parse($horaInicioUno)->format('H:i') > Carbon::parse($horaFinDos)->format('H:i')) {
            $rounTwoFijado = Carbon::parse($horaInicioDos)->diffInMinutes(Carbon::parse($horaFinDos)->addDay(), false);
        } else {
            $rounTwoFijado = Carbon::parse($horaInicioDos)->diffInMinutes(Carbon::parse($horaFinDos), false);
        }
        return   480;
        // return   $rounOneFijado + $rounTwoFijado;
    }

    // Asistencia
    public function Asistencia($fecha, $horaEntradaUno, $horaSalidaUno, $horaEntradaDos, $horaSalidaDos)
    {
        $asistenciaUno = Carbon::parse($fecha . $horaEntradaUno);
        $salidaUno =  Carbon::parse($fecha . $horaSalidaUno);
        $fechaSalida = Carbon::parse($fecha);
        $asistenciaDos = $horaEntradaDos;
        $salidaDos = $horaSalidaDos;

        if ($horaEntradaDos != 'null') {
            $asistenciaDos = Carbon::parse($fecha . $horaEntradaDos);
            $salidaDos =  Carbon::parse($fecha .  $horaSalidaDos);


            if (($horaSalidaUno >=   '00:00:00' && $horaSalidaUno <   $horaEntradaUno)) {
                $salidaUno->addDay();
                $fechaSalida->addDay();
            }

            if ($horaEntradaDos >= '00:00:00' &&  $horaSalidaUno <   $horaEntradaUno) {
                $asistenciaDos->addDay();
                $fechaSalida->addDay();
            }
        } else {
            if (($horaSalidaUno >=   '00:00:00' && $horaSalidaUno <   $horaEntradaUno)) {
                $salidaUno->addDay();
                $fechaSalida->addDay();
            }
        }

        if ($horaEntradaDos != 'null') {
            if (($asistenciaDos->format('Y-m-d') !=  $asistenciaUno->format('Y-m-d'))  || ($horaSalidaDos <    $horaEntradaUno)) {
                $salidaDos->addDay();
                $fechaSalida->addDay();
            }
        }
        return [$asistenciaUno, $salidaUno, $asistenciaDos,  $salidaDos,  $fechaSalida];
    }

    public function setTiempoExtra($cantidadEnMinutos)
    {
        $this->tiempoParaExtras - $cantidadEnMinutos;
    }
}
