<?php

namespace App\Traits;

use App\Console\Commands\Renovacion;
use App\Models\Contrato;
use App\Models\Renovacion as AliasRenovaciones;
use App\Models\Funcionario;
use App\Models\Preaviso;
use Carbon\Carbon;

trait RenovacionContrato
{
    public function renovaciones()
    {
        try {

            $funcionarios = $this->searchFuns();
            $funcionariosWithContracts = $this->funcionariosWithContracts($funcionarios);
            $this->renovar($funcionariosWithContracts);
        } catch (\Exception $th) {
            dump($th);
        }
    }

    public function searchFuns()
    {
        return Funcionario::select('id', 'nombres', 'apellidos', 'image')->whereHas('contractultimate.tipo_contrato', function ($query) {
            $query->where('nombre', 'like', '%Fijo%');
        })->with(['contratos' => function ($q) {
            $q->where('liquidado', 0)->whereNotNull('fecha_fin')->whereDate('fecha_fin', '<', Carbon::now()->addDays(30)->format('Y-m-d'));
        }])->withCount('preavisos')
            ->get();
    }


    public function funcionariosWithContracts($funcionarios)
    {
        $resultado = [];
        foreach ($funcionarios as $func) {
            if (count($func["contratos"]) > 0) {
                array_push($resultado, $func);
            }
        }
        return  $resultado;
    }


    public function renovar($funcionariosWithContracts)
    {
        foreach ($funcionariosWithContracts  as $funTemp) {
            $funcionarioContract =  $funTemp->contratos->first();
            $periodo =  ceil(Carbon::parse($funcionarioContract->fecha_inicio)
                ->floatDiffInMonths(Carbon::parse($funcionarioContract->fecha_fin)));
            $this->update($funcionarioContract, Carbon::parse($funcionarioContract->fecha_fin)->addMonths($periodo)->format('Y-m-d'),  $periodo);
        }
    }


    public function update($funcionarioContract, $newDate, $periodo)
    {

        $mesesAnio = 12;
        $vecesRenovadas = 3;

        if (!$this->hasPreaviso($funcionarioContract)) {
            if (Contrato::withCount('renovaciones')->find($funcionarioContract->id)->renovaciones_count > $vecesRenovadas) {
                $meses =  $mesesAnio - $periodo;
                $newDate = Carbon::parse($newDate)->addMonths($meses)->format('Y-m-d');
                $periodo = $mesesAnio;
            }

            $updateContract = $funcionarioContract->update([
                'fecha_fin' =>  $newDate
            ]);

            if ($updateContract) {
                AliasRenovaciones::create([
                    'contrato_id' => $funcionarioContract->id,
                    'periodo' => $periodo,
                ]);
            }
        }
    }

    public function hasPreaviso($funcionarioContract)
    {
        return  Preaviso::where('funcionario_id', $funcionarioContract->funcionario_id)
            ->whereNotNull('fecha_envio')->whereDate('fecha_envio', '>', Carbon::parse($funcionarioContract->fecha_fin)->subDays(45))
            ->first();
    }
}
