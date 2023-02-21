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

trait Encuesta
{

    public $funcionarios = [];

    public function encuestas()
    {

        try {

            $encuestas = $this->searchEncuestas();

            foreach ($encuestas as $encuesta) {

                $this->FuncionariosWithTurnoRotativo($encuesta);
                foreach ($this->funcionarios as $funcionario) {
                    if (count(EncuestaEnviadaFuncionario::where('funcionario_id',  $funcionario->id)
                        ->where('encuesta_id', $encuesta->id)
                        ->whereRaw('Date(created_at) = CURDATE()')
                        ->get()) == 0) {

                        Mail::to($funcionario->email)->send(new  EncuestaMail($funcionario));

                        EncuestaEnviadaFuncionario::create([
                            'encuesta_id' => $encuesta->id,
                            'funcionario_id' => $funcionario->id
                        ]);
                    }
                }
            }

            foreach ($encuestas as $encuesta) {
                $this->FuncionariosWithTurnoFijo($encuesta);
                foreach ($this->funcionarios as $funcionario) {
                    if (count(EncuestaEnviadaFuncionario::where('funcionario_id',  $funcionario->id)
                        ->where('encuesta_id', $encuesta->id)
                        ->whereRaw('Date(created_at) = CURDATE()')
                        ->get()) == 0) {
                            dump($funcionario->email);
                        Mail::to($funcionario->email)->send(new  EncuestaMail($funcionario));

                        EncuestaEnviadaFuncionario::create([
                            'encuesta_id' => $encuesta->id,
                            'funcionario_id' => $funcionario->id
                        ]);
                    }
                }
            }
        } catch (\Exception $th) {
            dump($th);
        }
    }

    public function searchEncuestas()
    {
        return   ModelsEncuesta::where('estado', 'Activa')->whereDate('fecha_inicio', '<=', Carbon::now()->format('Y-m-d'))->get();
    }

    public function FuncionariosWithTurnoRotativo($encuesta)
    {
        $this->funcionarios = [];
        $horarios = HorarioTurnoRotativo::with('funcionario')->whereHas('turnoRotativo', function ($query) use ($encuesta) {
            $query->whereBetween('hora_inicio_uno', [Carbon::now()->format('H:i'),  Carbon::now()->addMinutes($encuesta->minutos_anticipacion)->format('H:i')]);
        })
            ->whereDate('fecha', Carbon::now()->format('Y-m-d'))
            ->get();

        $this->fillInFuncionariosRotativos($horarios);
    }

    public function FuncionariosWithTurnoFijo($encuesta)
    {
        $translateService = new TranslateService();
        $this->funcionarios = [];
        $horarios = [];

        $horarios =  HorarioTurnoFijo::with('turnoFijo.funcionarios')
            ->where('dia', $translateService->translateDay(Carbon::now()->englishDayOfWeek))
            ->whereBetween('hora_inicio_uno', [Carbon::now()->format('H:i'),  Carbon::now()->addMinutes($encuesta->minutos_anticipacion)->format('H:i')])
            ->get();

        foreach ($horarios  as $horario) {
            $this->fillInFuncionariosFijos($horario->turnoFijo->funcionarios);
        }
    }

    public function fillInFuncionariosFijos($data)
    {
        foreach ($data  as $funcionario) {
            array_push($this->funcionarios, $funcionario);
        }
    }

    public function fillInFuncionariosRotativos($data)
    {
        foreach ($data  as $funcionario) {
            array_push($this->funcionarios, $funcionario->funcionario);
        }
    }
}
