<?php

namespace App\Http\Controllers;

use App\Exports\InformeByTiempoExport;
use App\Exports\InformeByLLegadasExport;
use App\Exports\LLegadasTarde;
use App\Exports\MasNovedadesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class DescargaController extends Controller
{
    public function informeTopArrival($fechaInicio, $fechaFin)
    {
        return Excel::download(new InformeByLLegadasExport($fechaInicio, $fechaFin), 'funcionarios-mayor-tiempo-acumulado.xlsx');
    }

    public function informeTopTime($fechaInicio, $fechaFin)
    {
        return Excel::download(new InformeByTiempoExport($fechaInicio, $fechaFin), 'funcionarios-mayor-tiempo-acumulado.xlsx');
    }

    public function getTopfuncionariosToDownload($fechaInicio, $fechaFin)
    {
        return Excel::download(new MasNovedadesExport($fechaInicio, $fechaFin), 'funcionarios-mayor-novedades-acumulado.xlsx');
    }
    public function reporteLlegadasExcel($fechaInicio, $fechaFin)
    {
        return Excel::download(new LLegadasTarde($fechaInicio, $fechaFin), 'funcionarios-llegadas-tarde.xlsx');
    }
}
