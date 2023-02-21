<?php

namespace App\Traits;

use App\Console\Commands\Renovacion;
use App\Mail\CongratulationMail;
use App\Models\Contrato;
use App\Models\Renovacion as AliasRenovaciones;
use App\Models\Funcionario;
use App\Models\Preaviso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait Congratulation
{

    public function congratulations()
    {
        try {

            $funcionarios = $this->searchFuns();
            $this->emails($funcionarios);
        } catch (\Exception $th) {
            dump($th);
        }
    }

    public function searchFuns()
    {
        DB::statement("SET lc_time_names = 'es_ES'");
        return Funcionario::select(
            'nombres',
            'apellidos',
            'email',
            DB::raw('DATE_FORMAT(fecha_nacimiento,"%d %M") as fecha')
        )->where(function ($q) {
            $q->whereDay('fecha_nacimiento', Carbon::now()->day);
        })->orderBy("fecha_nacimiento", "desc")->get();
    }


    public function emails($funcionarios)
    {
        foreach ($funcionarios  as $funTemp) {
            Mail::to($funTemp->email)->send(new  CongratulationMail($funTemp));
        }
    }
}
