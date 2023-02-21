<?php

namespace App\Traits;

use App\Models\Configuracion;
use App\Models\ConfiguracionEmpresa;
use App\Models\Festivo;
use App\Models\Funcionario;
use App\Models\TurnoRotativo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait calcularExtras
{

    public  $tiempoParaExtras;


    public function calcularExtrasRotativo($funcionario)
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

                        $turno =   TurnoRotativo::find($day[0]['day']['turno_rotativo_id'])->toArray();

                        // in_array($info->asistencia->format('Y-m-d'), $this->getFestivos())

                        $toleranciaSalida = $turno['tolerancia_salida'];

                        $cotizaExtras = $turno['extras'];
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


                        //Asistencia
                        [$llegada, $salida, $fechaSalida] =   $this->Asistencia($laborado['fecha'], $laborado['hora_entrada_uno'], $laborado['hora_salida_uno']);

                        //Tiempo Laborado
                        $rounOne = $llegada->diffInMinutes($salida, false);
                        $tiempoLaborado = 0;

                        $salidaDos =  $salida;
                        $rounOne = $llegada->diffInMinutes($salida, false);
                        $tiempoLaborado  = $rounOne;

                        $FinTurnoReal =  $llegada->copy()->addMinutes($tiempoLaborado);
                        $FinTurnoOfficial =  $llegada->copy()->addMinutes($tiempoAsignado);


                        //Compruebo si trabajo mas del tiempo fijado y la tolerancia (Si trabajó extras)
                        if ($tiempoLaborado > ($tiempoAsignado + $toleranciaSalida)) {

                            //Obtengo El tiempo que sera extra
                            $this->tiempoParaExtras =  abs($tiempoAsignado - $tiempoLaborado);

                            /************************************************************************Recargos cuando se tiene horas extas  *********************************************************************************/

                            if ($FinTurnoReal->hour > 21 || $FinTurnoReal->hour == 00) {
                                if ($this->translateService->translateDay($FinTurnoReal->englishDayOfWeek == 'Sunday')) {
                                    $horasRecargoDominicalNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos->addDay());
                                }
                                if ($FinTurnoReal->hour == 00) {
                                    $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos->addDay());
                                } else {
                                    $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '21:00:00')->diffInMinutes($salidaDos);
                                }
                            }

                            if ($FinTurnoReal->hour > 00 && $FinTurnoReal->hour <= 06) {

                                if ($this->translateService->translateDay($FinTurnoReal->englishDayOfWeek == 'Sunday')) {
                                    $horasRecargoDominicalNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '00:00:00')->diffInMinutes($salidaDos);
                                }

                                if ($llegada->hour <= 21) {
                                    $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') .  '21:00:00')->diffInMinutes($salidaDos->addDay()->format('Y-m-d' . '00:00.00'));
                                } else {
                                    $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '00:00:00')->diffInMinutes($salidaDos);
                                }
                            }

                            /***********************************************************************************************************************************************************************************************/

                            //  Compruebo si Solo se le pagan horas nopturnas
                            if ($FinTurnoReal->hour > 21 ||  ($FinTurnoReal->hour > 00 &&  $FinTurnoReal->hour <= 06)) {
                                $HorasExtrasNocturnas += $this->tiempoParaExtras;
                                $this->setTiempoExtra($this->tiempoParaExtras);
                            }

                            // Compruebo si Solo se le pagan horas extras diurnas
                            if ($FinTurnoReal->hour <= 21 && $FinTurnoReal->hour >  06) {

                                // verifico que no me tome el siguiente dia si el turno temina  a las 00
                                if ($FinTurnoReal->diffInMinutes($salidaDos->format('Y-m-d')  . '21:00:00') / 60 >= 24) {
                                    $tiempoParaInicioNocturno = $FinTurnoReal->diffInMinutes($salidaDos->subDay()->format('Y-m-d')  . '21:00:00');
                                } else {
                                    $tiempoParaInicioNocturno = $FinTurnoReal->diffInMinutes($salidaDos->format('Y-m-d')  . '21:00:00');
                                }
                                // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                                if ($this->tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                                    // Asigno todo el tiempo Extra como diurno

                                    $HorasExtrasDiurnas = $this->tiempoParaExtras;
                                    $FinTurnoReal->addMinutes($HorasExtrasDiurnas);
                                    $this->setTiempoExtra($HorasExtrasDiurnas);
                                } else {
                                    // Asigno parte del tiempo a extras diurnas
                                    $HorasExtrasDiurnas = $tiempoParaInicioNocturno;
                                    $FinTurnoReal->addMinutes($HorasExtrasDiurnas);

                                    //Obtengo el resto de horas que sumaran como nocturnas
                                    $this->tiempoParaExtras = $this->tiempoParaExtras - $HorasExtrasDiurnas;

                                    if ($llegada->format('Y-m-d') != $salidaDos->format('Y-m-d')) {
                                        $tiempoParaInicioSiguienteDia = $FinTurnoReal->diffInMinutes(Carbon::parse($salidaDos->format('Y-m-d') . '00:00:00'));
                                    } else {
                                        $tiempoParaInicioSiguienteDia = $FinTurnoReal->diffInMinutes(Carbon::parse($salidaDos->format('Y-m-d') . '00:00:00')->addDay());
                                    }

                                    if ($this->tiempoParaExtras - $tiempoParaInicioSiguienteDia < 0) {
                                        // Asigno todo el tiempo extra nocturno el mismo dia
                                        $HorasExtrasNocturnas += $this->tiempoParaExtras;
                                        $FinTurnoReal->addMinutes($HorasExtrasNocturnas);
                                        $this->setTiempoExtra($this->tiempoParaExtras);
                                    } else {
                                        // Asigno parte del tiempo extra nocturno el mismo dia
                                        $HorasExtrasNocturnas += $tiempoParaInicioSiguienteDia;
                                        $FinTurnoReal->addMinutes($HorasExtrasNocturnas);
                                        $this->tiempoParaExtras = $this->tiempoParaExtras - $tiempoParaInicioSiguienteDia;
                                        $tiempoParaInicioNuevoDiurno = $FinTurnoReal->diffInMinutes(Carbon::parse($salidaDos->format('Y-m-d') . '06:00:00'));

                                        if ($this->translateService->translateDay(Carbon::parse($FinTurnoReal)->englishDayOfWeek) == 'Domingo' ||  in_array($llegada->format('Y-m-d'), $this->getFestivos())) {
                                            
                                            if ($this->tiempoParaExtras - $tiempoParaInicioNuevoDiurno < 0) {
                                                // Asigno todo el tiempo extra nocturno siguiente dia
                                                $HorasExtrasNocturnasDominicales = $this->tiempoParaExtras;
                                                $FinTurnoReal->addMinutes($HorasExtrasNocturnasDominicales);
                                                $this->setTiempoExtra($this->tiempoParaExtras);
                                            } else {
                                                // Asigno parte del tiempo extra nocturno siguiente dia
                                                $HorasExtrasNocturnasDominicales = $tiempoParaInicioSiguienteDia;
                                                $FinTurnoReal->addMinutes($HorasExtrasNocturnasDominicales);
                                                $this->tiempoParaExtras = $this->tiempoParaExtras - $tiempoParaInicioSiguienteDia;

                                                //Horas Diurnas Siguiente Dia
                                                $tiempoParaInicioNocturno = $FinTurnoReal->diffInMinutes($salidaDos->format('Y-m-d') . '21:00:00');
                                                // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                                                if ($this->tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                                                    // Asigno todo el tiempo Extra como diurno
                                                    $HorasExtrasDiurnasDominicales =  $HorasExtrasDiurnasDominicales + $this->tiempoParaExtras;
                                                    $FinTurnoReal->addMinutes($HorasExtrasDiurnasDominicales);
                                                    $this->setTiempoExtra($this->tiempoParaExtras);
                                                }
                                            }
                                        }
                                        if ($this->tiempoParaExtras - $tiempoParaInicioNuevoDiurno < 0) {
                                            // Asigno todo el tiempo extra nocturno siguiente dia
                                            $HorasExtrasNocturnas += $this->tiempoParaExtras;
                                            $FinTurnoReal->addMinutes($HorasExtrasNocturnas);
                                            $this->setTiempoExtra($this->tiempoParaExtras);
                                        } else {
                                            // Asigno parte del tiempo extra nocturno siguiente dia
                                            $HorasExtrasNocturnas = $tiempoParaInicioSiguienteDia;
                                            $FinTurnoReal->addMinutes($HorasExtrasNocturnas);
                                            $this->tiempoParaExtras = $this->tiempoParaExtras - $tiempoParaInicioSiguienteDia;
                                            //Horas Diurnas Siguiente Dia
                                            $tiempoParaInicioNocturno = $FinTurnoReal->diffInMinutes($salidaDos->format('Y-m-d') . '21:00:00', false);
                                            // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                                            if ($this->tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                                                // Asigno todo el tiempo Extra como diurno
                                                $HorasExtrasDiurnas =  $HorasExtrasDiurnas + $this->tiempoParaExtras;
                                                $FinTurnoReal->addMinutes($HorasExtrasDiurnas);
                                                $this->setTiempoExtra($this->tiempoParaExtras);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            /*******************************************************************Calculo recargos cuando no hay Extras*****************************************************************/
                            [$salidaDos,  $FinTurnoReal,  $horasRecargoDominicalNocturna,  $horasRecargoNocturna, $asistenciaUno,  $horasRecargoDominicalDiurno] = $this->calcularRecargoRotativo($salidaDos,  $FinTurnoReal,  $horasRecargoDominicalNocturna,  $horasRecargoNocturna, $salida,  $horasRecargoDominicalDiurno);
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
        return   $rounOneFijado + $rounTwoFijado;
    }

    // Asistencia
    public function Asistencia($fecha, $horaEntradaUno, $horaSalidaUno)
    {
        $asistenciaUno = Carbon::parse($fecha . $horaEntradaUno);
        $salidaUno =  Carbon::parse($fecha . $horaSalidaUno);
        $fechaSalida = Carbon::parse($fecha);


        if (($horaSalidaUno >=   '00:00:00' && $horaSalidaUno <   $horaEntradaUno)) {
            $salidaUno->addDay();
            $fechaSalida->addDay();
        }

        return [$asistenciaUno, $salidaUno,  $fechaSalida];
    }

    public function setTiempoExtra($cantidadEnMinutos)
    {
        $this->tiempoParaExtras - $cantidadEnMinutos;
    }


    public function calcularRecargoRotativo($salida, $FinTurnoReal,  $horasRecargoDominicalNocturna,  $horasRecargoNocturna, $horaLlegada,  $horasRecargoDominicalDiurno)
    {

        if ($salida->hour > 21 || $salida->hour == 00) {

            if ($horaLlegada->hour >= 21) {
                $horasRecargoNocturna += Carbon::parse($horaLlegada)->diffInMinutes($salida);
            } else {
                $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '21:00:00')->diffInMinutes($salida);
            }

        
            if ($this->translateService->translateDay($FinTurnoReal->englishDayOfWeek == 'Sunday')) {
                $horasRecargoDominicalNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '21:00:00')->diffInMinutes($salida->addDay());
            }
            if ($salida->hour == 00) {
                $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '21:00:00')->diffInMinutes($salida->addDay());
            }
        }

        if ($salida->hour > 00 && $salida->hour <= 6) {


            if ($this->translateService->translateDay($FinTurnoReal->englishDayOfWeek == 'Sunday')) {
                $horasRecargoDominicalNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '00:00:00')->diffInMinutes($salida);
            }

            $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') . '00:00:00')->diffInMinutes($salida);

            if ($horaLlegada->hour <= 21) {
                $horasRecargoNocturna += Carbon::parse($FinTurnoReal->format('Y-m-d') .  '21:00:00')->diffInMinutes($salida->addDay()->format('Y-m-d' . '00:00.00'));
            }
        }

        if ($salida->hour > 06 && $salida->hour < 21) {
            if ($this->translateService->translateDay($FinTurnoReal->englishDayOfWeek == 'Sunday')) {
                $horasRecargoDominicalDiurno += Carbon::parse($FinTurnoReal->format('Y-m-d') . '06:00:00')->diffInMinutes($salida);
                if ($horaLlegada->hour <= 21) {
                    $horasRecargoDominicalDiurno += Carbon::parse($FinTurnoReal->format('Y-m-d') .  '21:00:00')->diffInMinutes($salida->addDay()->format('Y-m-d' . '00:00.00'));
                }
            }
        }

        return [$salida,  $FinTurnoReal,  $horasRecargoDominicalNocturna,  $horasRecargoNocturna, $horaLlegada,  $horasRecargoDominicalDiurno];
    }
}
