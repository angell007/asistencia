<?php

namespace App\Traits;

use App\Models\Festivo;
use Illuminate\Support\Facades\DB;

trait Festivos
{

    public  $festivos;

    public  function getFestivos()
    {
        return  Festivo::select(DB::raw('fecha as date'))->pluck('date')->toArray();
    }
}
