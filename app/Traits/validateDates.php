<?php

namespace App\Traits;

use App\Models\Novedad;
use Carbon\Carbon;

trait validateDates
{

    public $data;
    public $fecha_inicio;
    public $fecha_fin;

    public function customValidate(array $request): bool
    {
        $novedad = Novedad::where('funcionario_id', $request['funcionario_id'])->latest()->first();

        if ($novedad == null) {
            return true;
        }

        $this->data = $request;
        $this->fecha_inicio = $novedad->fecha_inicio;
        $this->fecha_fin =  $novedad->fecha_fin;

        if ($this->ComparateDates($request['fecha_inicio'])) {
            return false;
        }
        if ($this->ComparateDates($request['fecha_fin'])) {
            return false;
        }

        return true;
    }



    public function ComparateDates($dateToCompare)
    {
        if (Carbon::parse($dateToCompare)->between($this->fecha_inicio,   $this->fecha_fin)) {
            return true;
        }
        return false;
    }
}
