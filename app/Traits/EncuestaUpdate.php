<?php

namespace App\Traits;

use App\Console\Commands\Renovacion;
use App\Mail\CongratulationMail;
use App\Mail\EncuestaMail;
use App\Models\Encuesta as ModelsEncuesta;
use App\Models\EncuestaEnviadaFuncionario;
use App\Models\HorarioTurnoFijo;
use App\Models\HorarioTurnoRotativo;
use App\Services\TranslateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

trait EncuestaUpdate
{

    public $funcionarios = [];

    public function encuestas()
    {

        try {

            $encuestas = $this->searchEncuestas();

            foreach ($encuestas as $encuesta) {
                $this->renovar($encuesta->frecuencia_aplicacion, $encuesta);
            }
            
        } catch (\Exception $th) {
            dump($th);
        }
    }

    public function searchEncuestas()
    {
        return   ModelsEncuesta::where('estado', 'Activa')->whereDate('fecha_inicio', Carbon::now()->format('Y-m-d'))->get();
    }

    public function renovar($frequency, $encuesta)
    {
        switch ($frequency) {
            case 'unica':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addCenturies(1)->format('Y-m-d')
                ]);
                break;
            case 'diaria':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addDay()->format('Y-m-d')
                ]);
                break;
            case 'semanal':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addWeek()->format('Y-m-d')
                ]);
                break;
            case 'quincenal':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addWeeks(2)->format('Y-m-d')
                ]);
                break;
            case 'mensual':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addMonth()->format('Y-m-d')
                ]);
                break;
            case 'bimensual':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addMonths(2)->format('Y-m-d')
                ]);
                break;
            case 'trimestral':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addMonths(3)->format('Y-m-d')
                ]);
                break;
            case 'semestral':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addMonths(6)->format('Y-m-d')
                ]);
                break;
            case 'anual':
                $encuesta->update([
                    'fecha_inicio' => Carbon::now()->addYear()->format('Y-m-d')
                ]);
                break;
        }
    }
}
