<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Funcionario;
use App\Models\LlegadaTarde;
use App\Models\Novedad;
use App\Models\Festivo;
use App\Models\Frase;
use App\Models\PagoNomina;
use App\Services\TranslateService;
use Illuminate\Support\Facades\DB;

class TableroController extends Controller
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct(TranslateService $service)
    {
        $this->service = $service;
        $this->fechaInicio = Carbon::now()->startOfMonth()->toDateString();
        $this->fechaFin = Carbon::now()->endOfMonth()->toDateString();
    }
    /**
     * Obtener los funcionarios con más llegadas tarde en el mes actual
     *
     * @return void
     */
    public function getTopFuncionariosByLlegadas()
    {
        return Funcionario::from('funcionario as func')->join('llegada_tarde as llt', 'llt.funcionario_id', 'func.id')->select('func.id', 'func.nombres', 'func.apellidos', 'func.image', 'func.dependencia_id', DB::raw('count(llt.id) as llegadas'))->whereBetween('llt.fecha', [$this->fechaInicio, $this->fechaFin])->groupBy('func.id')->orderBy('llegadas', 'asc')->take(5)->with('dependencia:id,nombre')->get();
    }

    public function getLlegadasByFecha()
    {
        return LlegadaTarde::whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])->orderBy('fecha')->get(['id', 'fecha', 'tiempo'])->groupBy('fecha');
    }

    public function getBirthdayFuncionarios()
    {

        DB::statement("SET lc_time_names = 'es_ES'");
        return Funcionario::select(
            'nombres',
            'apellidos',
            'image',
            DB::raw('DATE_FORMAT(fecha_nacimiento,"%d %M") as fecha')
        )->where(function ($q) {
            $q->whereMonth('fecha_nacimiento', Carbon::now()->month);
        })->orderBy("fecha_nacimiento", "desc")->take(3)->get();
    }

    public function getAllBirthdayFuncionarios()
    {
        DB::statement("SET lc_time_names = 'es_ES'");
        return Funcionario::select(
            'nombres',
            'apellidos',
            DB::raw('DATE_FORMAT(fecha_nacimiento,"%d %M") as fecha')
        )->where(function ($q) {
            $q->whereMonth('fecha_nacimiento', Carbon::now()->month);
        })->orderBy("fecha_nacimiento", "desc")->get();
    }

    public function getVencimientoContratos()
    {
        //TODO filtro para asegurar los preavisos sean correctos

        $funcionarios = Funcionario::select('id', 'nombres', 'apellidos', 'image')->whereHas('contractultimate.tipo_contrato', function ($query) {
            $query->where('nombre', 'like', '%Fijo%');
        })->with(['contratos' => function ($q) {
            $q->where('liquidado', 0)->whereNotNull('fecha_fin')->whereBetween('fecha_fin', [Carbon::now()->addDays(30), Carbon::now()->addDays(45)])->orderBy('id', 'Desc');
        }])->withCount('preavisos')
            ->get();

        $resultado = [];

        foreach ($funcionarios as $func) {
            if (count($func["contratos"]) > 0) {
                array_push($resultado, $func);
            }
        }
        return $resultado;
    }


    public function getContratosWithPreavisoNotRenovados()
    {
        try {
            $funcionarios = Funcionario::select('id', 'nombres', 'apellidos', 'image')->whereHas('contractultimate.tipo_contrato', function ($query) {
                $query->where('nombre', 'like', '%Fijo%');
            })->with(['contratos' => function ($q) {
                $q->where('liquidado', 0)->whereNotNull('fecha_fin')
                    ->whereDate('fecha_fin', '<', Carbon::now()->addDays(31))
                    ->whereDate('fecha_fin', '>=', Carbon::now())
                    ->orderBy('id', 'Desc');
            }])->withCount('preavisos')
                ->get();

            $resultado = [];

            foreach ($funcionarios as $func) {
                if (count($func["contratos"]) > 0) {
                    array_push($resultado, $func);
                }
            }
            return $resultado;
        } catch (\Exception $th) {
            dump($th);
        }
    }



    public function getCostosByFecha()
    {

        $mes_actual = Carbon::parse($this->fechaFin)->subMonth(5)->format("m");
        $ano_actual = Carbon::parse($this->fechaFin)->subMonth(5)->format("Y");
        $resultado = [];
        $z = 0;

        //return($mes_actual);

        while ($z < 6) {
            if ($mes_actual == 13) {
                $mes_actual = 1;
                $ano_actual++;
            }

            $pagos = PagoNomina::where('fin_periodo', 'like', '%' . ($ano_actual . '-' . $mes_actual) . '%')->get();
            $salarios = 0;
            $provisiones = 0;
            $seguridad = 0;

            foreach ($pagos as $pago) {
                $salarios += $pago["total_salarios"] + $pago["total_retenciones"] + $pago["total_parafiscales"];
                $provisiones += $pago["total_provisiones"];
                $seguridad += $pago["total_seguridad_social"];
            }

            $data = [
                'mes' => $this->service->translateMonth($mes_actual),
                'salarios' =>        number_format(($salarios / 1000000), 1, ".", ""),
                'provisiones' =>     number_format(($provisiones / 1000000), 1, ".", ""),
                'seguridad' =>       number_format(($seguridad / 1000000), 1, ".", "")
            ];
            array_push($resultado, $data);
            $mes_actual++;
            $z++;
        }

        return $resultado;
    }

    public function getIndicadores()
    {
        $llegadas = LlegadaTarde::whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])->orderBy('fecha')->get(['id']);
        $novedades = Novedad::whereBetween('fecha_inicio', [$this->fechaInicio, $this->fechaFin])->orderBy('fecha_inicio')->get(['id']);
        // $nuevos = Funcionario::whereBetween('fecha_ingreso', [$this->fechaInicio, $this->fechaFin])->orderBy('fecha_ingreso')->get(['id']);

        $nuevos = Funcionario::with([
            'contractultimate' =>
            function ($q) {
                $q->whereBetween('fecha_inicio', [$this->fechaInicio, $this->fechaFin]);
            }
        ])->get(['id']);

        $datos = array(
            'Llegadas' => count($llegadas),
            'Novedades' => count($novedades),
            'Renuncias' => 0,
            'Nuevos' => count($nuevos)
        );
        return $datos;
    }
    public function getFestivos()
    {

        return Festivo::select(
            'nombre',
            'fecha'
            // DB::raw('DATE_FORMAT(fecha,"%d %b") as fecha')
        )->whereDate('fecha', '>=', Carbon::now()->format('Y-m-d'))->orderBy('fecha', 'Asc')->take(3)->get();

        // DB::statement("SET lc_time_names = 'es_ES'");
        // return Festivo::select('nombre', DB::raw(
        //     'DATE_FORMAT(fecha,"%d %b") as fechax'
        // ))->whereDate('fecha', '>=', Carbon::today()->toDateString())->orderBy('fecha', 'Desc')->take(3)->get();
    }
    public function getFrase()
    {
        try {
            return Frase::inRandomOrder()->first();
        } catch (\Exception $th) {
            dump($th);
        }
    }
}
