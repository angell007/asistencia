<?php

namespace App\Http\Controllers;

use App\Models\ContableIngreso;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Novedad;
use App\Http\Libs\Nomina\Calculos\CalculoProvisiones;
use App\Http\Libs\Nomina\Calculos\ResumenPago;

use App\Http\Libs\Nomina\Facades\NominaExtras;
use App\Http\Libs\Nomina\Facades\NominaNovedades;
use App\Http\Libs\Nomina\Facades\NominaIngresos;
use App\Http\Libs\Nomina\Facades\NominaSalario;
use App\Http\Libs\Nomina\Facades\NominaRetenciones;
use App\Http\Libs\Nomina\Facades\NominaDeducciones;
use App\Http\Libs\Nomina\Facades\NominaSeguridad;
use App\Http\Libs\Nomina\Facades\NominaProvisiones;
use App\Http\Libs\Nomina\Facades\NominaPago;
use App\Http\Requests\PagosNominaStoreRequest;
use App\Models\Deduccion;
use App\Models\NominaSeguridadSocialFuncionario;
use Carbon\Carbon;
use App\Models\PagoNomina;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;


class PagosNominaController extends Controller
{

    protected $diasPeriodo;

    public function getFuncionario($identidad)
    {
        $funcionario = Funcionario::where('identidad', '=', $identidad)->with('cargo', 'contractultimate')->first();
        if (!$funcionario) {
            return response()->json(['message' => 'Funcionario no encontrado'], 404);
        }

        return $funcionario;
    }
    /**
     * Calcular la cantidad y total de horas extras y recargos acumulados del funcionario en el periodo
     *
     * @param int $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getExtrasTotales($id, $fechaInicio, $fechaFin)
    {
        return NominaExtras::extrasFuncionarioWithId($id)->fromTo($fechaInicio, $fechaFin);
    }
    /**
     * Calcular la cantidad y total de novedades del funcionario en el periodo
     *
     * @param int $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getNovedades($id, $fechaInicio, $fechaFin)
    {
        return NominaNovedades::novedadesFuncionarioWithId($id)->fromTo($fechaInicio, $fechaFin)->calculate();
    }

    public function getIngresos($id, $fechaInicio, $fechaFin)
    {
        return NominaIngresos::ingresosFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getSalario($id, $fechaInicio, $fechaFin)
    {
        return NominaSalario::salarioFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getRetenciones($id, $fechaInicio, $fechaFin)
    {
        return  NominaRetenciones::retencionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getDeducciones($id, $fechaInicio, $fechaFin)
    {
        return NominaDeducciones::deduccionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getSeguridad($id, $fechaInicio, $fechaFin)
    {
        return NominaSeguridad::seguridadFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getProvisiones($id, $fechaInicio, $fechaFin)
    {
        return NominaProvisiones::provisionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }


    public function getPagoNeto($id, $fechaInicio, $fechaFin)
    {

        return NominaPago::pagoFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getPagoNetoCustom($id, $fechaInicio, $fechaFin)
    {

        return  NominaPago::pagoFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->customCalculate();
    }


    public function getIngresosPrestacionales()
    {
        return ContableIngreso::where('tipo', '=', 'Constitutivo')->get();
    }

    public function getIngresosNoPrestacionales()
    {
        return ContableIngreso::where('tipo', '=', 'No Constitutivo')->get();
    }


    public function getPagoFuncionarios($inicio = null, $fin = null)
    {
        $frecuenciaPago =  Empresa::get(['frecuencia_pago'])->first()['frecuencia_pago'];
        $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
        $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");
        $funcionariosResponse = [];

        if ($frecuenciaPago === 'Quincenal') {
            if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                $fechaInicioPeriodo = date("Y-m-16 00:00:00");
            } else {
                $fechaFinPeriodo =     date("Y-m-15 23:59:59");;
            }
        }

        $fechaInicioPeriodo = $fechaInicioPeriodo;
        $fechaFinPeriodo = $fechaFinPeriodo;

        /**
         * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
         * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
         * desde el componente de historial de pagos en Vue cuando se realiza una petición.
         */
        if ($inicio) {
            $fechaInicioPeriodo = $inicio;
        }

        if ($fin) {
            $fechaFinPeriodo = $fin;
        }

        $fechasNovedades = function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereBetween('fecha_inicio', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('fecha_fin', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('novedad');
        };

        $funcionarios = Funcionario::whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereDate('fecha_inicio', '<=', $fechaFinPeriodo)->whereDate('fecha_fin', '>=', $fechaInicioPeriodo)->orWhereNull('fecha_fin');
        })->with(['novedades' => $fechasNovedades])->get();


        foreach ($funcionarios as $funcionario) {

            $funcionariosResponse[] = response()->json([
                'id' => $funcionario->id,
                'identidad' => $funcionario->identidad,
                'nombres' => $funcionario->nombres,
                'apellidos' => $funcionario->apellidos,
                'image' => $funcionario->image,
                'salario_neto' => $this->getPagoNeto($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'],
                'novedades' => $funcionario->novedades,
                'horas_extras' => $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['horas_reportadas'],
                'novedades' => $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['novedades'],

                'valor_ingresos_salariales' => ($this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_constitutivos'] +
                    $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'] +
                    $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']),
                'valor_ingresos_no_salariales' => $this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_no_constitutivos'],
                'valor_deducciones' => $this->getDeducciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']
            ]);
        }

        return $funcionariosResponse;
    }

    /**
     * Retorna el resumen del pago de nómina en el mes actual, si ya existe en la BD entonces se modifica la  respuesta agregando propiedades a la respuesta
     *
     * @return Json
     */
    public function getPagoNomina($inicio = null, $fin = null)
    {
        $frecuenciaPago =  Empresa::get(['frecuencia_pago'])->first()['frecuencia_pago'];
        $pagoNomina = $nomina = $paga = $idNominaExistente = null;

        $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
        $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");

        $totalSalarios = 0;
        $totalRetenciones = 0;
        $totalSeguridadSocial = 0;
        $totalParafiscales = 0;
        $totalProvisiones = 0;
        $totalExtras = 0;
        $totalIngresos = 0;
        $totalCostoEmpresa = 0;

        if ($frecuenciaPago === 'Quincenal') {
            if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                $fechaInicioPeriodo = date("Y-m-16 00:00:00");
            } else {
                $fechaFinPeriodo = date("Y-m-15 23:59:59");
            }
        }

        $fechaInicioPeriodo = $fechaInicioPeriodo;
        $fechaFinPeriodo = $fechaFinPeriodo;


        /**
         * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
         * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
         * desde el componente de historial de pagos en Vue cuando se realiza una petición.
         */
        if ($inicio) {
            $fechaInicioPeriodo = $inicio;
        }

        if ($fin) {
            $fechaFinPeriodo = $fin;
        }

        /**
         * Comprobar si ya existe un pago de nómina en el periodo
         */

        $nomina = PagoNomina::latest()->first();

        if ($nomina) {
            $idNominaExistente = $nomina->id;
            $paga = Carbon::now()->between($nomina->inicio_periodo, $nomina->fin_periodo);
        }

        $funcionarios = Funcionario::whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereDate('fecha_inicio', '<=', $fechaFinPeriodo)->whereDate('fecha_fin', '>=', $fechaInicioPeriodo)->orWhereNull('fecha_fin');
        })->with(['novedades' => function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereBetween('fecha_inicio', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('fecha_fin', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('novedad');
        }])->get();


        foreach ($funcionarios as $funcionario) {

            $salario = $this->getPagoNeto($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'];
            $retencion = $this->getRetenciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];
            $seguridad = $this->getSeguridad($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total_seguridad'];
            $parafiscal = $this->getSeguridad($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total_parafiscales'];
            $provision = $this->getProvisiones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];
            $extras = $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];
            $ingresos = $this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];

            $totalSalarios +=  $salario;
            $totalRetenciones += $retencion;
            $totalSeguridadSocial += $seguridad;
            $totalParafiscales += $parafiscal;
            $totalProvisiones += $provision;
            $totalExtras += $extras;
            $totalIngresos += $ingresos;

            $funcionariosResponse[] = response()->json([
                'id' => $funcionario->id,
                'identidad' => $funcionario->identidad,
                'nombres' => $funcionario->nombres,
                'apellidos' => $funcionario->apellidos,
                'image' => $funcionario->image,
                'salario_neto' => $salario,
                'novedades' => $funcionario->novedades,
                'horas_extras' => $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['horas_reportadas'],
                'novedades' => $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['novedades'],

                'valor_ingresos_salariales' => ($this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_constitutivos'] +
                    $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'] +
                    $extras),
                'valor_ingresos_no_salariales' => $this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_no_constitutivos'],
                'valor_deducciones' => $this->getDeducciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']
            ]);
        }

        $totalCostoEmpresa += $totalSalarios + $totalRetenciones +   $totalSeguridadSocial + $totalParafiscales + $totalProvisiones;

        $pagoNomina = response()->json([
            'frecuencia_pago' => $frecuenciaPago,
            'inicio_periodo' => $fechaInicioPeriodo,
            'fin_periodo' => $fechaFinPeriodo,
            'salarios' =>  $totalSalarios,
            'seguridad_social' => $totalSeguridadSocial,
            'parafiscales' => $totalParafiscales,
            'provisiones' => $totalProvisiones,
            'extras' => $totalExtras,
            'ingresos' => $totalIngresos,
            'retenciones' => $totalRetenciones,
            'costo_total_empresa' => $totalCostoEmpresa,
            'nomina_paga' => $paga,
            'nomina_paga_id' => $idNominaExistente,
            'funcionarios' => $funcionariosResponse
        ]);

        return $pagoNomina;
    }

    /**
     * Obtener el desprendible o colilla de pago del funcionario
     *
     * @param int $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return file
     */
    public function getColillaFuncionario($id, $fechaInicio, $fechaFin)
    {

        $funcionarioDB = Funcionario::with('contractultimate', 'cargo')->find($id);


        $empresa = Empresa::first();
        if (!$empresa) {
            $empresa = 'No Registrada';
        }

        $auxilioTransporte = $funcionarioDB->contractultimate->subsidio_transporte ? $empresa->auxilio_transporte : 0;

        $novedades = $this->getNovedades($id, $fechaInicio, $fechaFin);

        $retenciones = $this->getRetenciones($funcionarioDB->id, $fechaInicio, $fechaFin);

        $infoCompletePago = $this->getPagoNetoCustom($funcionarioDB->id, $fechaInicio, $fechaFin);


        $this->diasPeriodo = Carbon::parse($fechaFin)->diffInDays(Carbon::parse($fechaInicio)) + 1;


        $funcionario = collect([
            'empresa' => $empresa->razon_social,
            'tipo_doc' => $empresa->tipo_documento,
            'num_doc' => $empresa->numero_documento,
            'identidad' => $funcionarioDB->identidad,
            'nombres' => ucwords($funcionarioDB->nombres),
            'apellidos' => ucwords($funcionarioDB->apellidos),
            'cargo' => $funcionarioDB->cargo->nombre,
            'inicio' => Carbon::parse($fechaInicio)->toFormattedDateString(),
            'fin' => Carbon::parse($fechaFin)->toFormattedDateString(),
            'salario_periodo' => $infoCompletePago['valorSalario'],
            'salario_base' => $funcionarioDB->contractultimate->salario,
            'auxilio_transporte' =>  $infoCompletePago['valorAuxilioTransporte'],
            'periodo' => (($this->diasPeriodo > 30) ? 30 : (($this->diasPeriodo > 16 && $this->diasPeriodo < 30) ? 30 : ((($this->diasPeriodo > 15 && $this->diasPeriodo < 28) || $this->diasPeriodo < 14)  ? 15 : $this->diasPeriodo))) - $novedades['total_dias'],
            'salario_neto' => $this->calculateNeto($infoCompletePago),
            'retenciones' => $infoCompletePago['valorRetenciones'],
            'deducciones' => $infoCompletePago['valorDeducciones'],
            'horas_extras' =>  $infoCompletePago['valorHorasExtras'],
            'adicionales' =>  $infoCompletePago['valorIngresosAdicionales'],
            'comodin' => $this->getRetenciones($funcionarioDB->id, $fechaInicio, $fechaFin),
            'novedades' => $novedades,
        ]);

        $nombrePdf = $funcionario['nombres'] . '_' . $funcionario['apellidos'] . '.pdf';
        return PDF::loadView('nomina.colilla', compact('funcionario'))->download($nombrePdf);
    }


    public function store(PagosNominaStoreRequest $pagosNominaStoreRequest)
    {

        $atributos = request()->all();

        $atributos['inicio_periodo'] = Carbon::parse(request()->get('inicio_periodo'))->format('Y-m-d');
        $atributos['fin_periodo'] =  Carbon::parse(request()->get('fin_periodo'))->format('Y-m-d');

        $pagoNomina = PagoNomina::create($atributos);
        $funcionarios = Funcionario::where('liquidado', false)->get();

        $funcionarios->each(function ($funcionario) use ($pagoNomina) {

            $salario =  $this->getSalario($funcionario->id, $pagoNomina->inicio_periodo, $pagoNomina->fin_periodo);

            $pagoNomina->pagosNominaFuncionario()->create([
                'funcionario_id' => $funcionario->id,
                'pago_nomina_id' => $pagoNomina->id,
                'dias_trabajados' => $salario['dias_trabajados'],
                'salario' => $salario['salario'],
                'auxilio_transporte' => $salario['auxilio_transporte'],
                'retenciones_deducciones' => $this->getRetenciones($funcionario->id, $pagoNomina->inicio_periodo, $pagoNomina->fin_periodo)['valor_total'],
                'salario_neto' => $this->getPagoNeto($funcionario->id, $pagoNomina->inicio_periodo, $pagoNomina->fin_periodo)['total_valor_neto'],
            ]);

            $previsiones =  $this->getProvisiones($funcionario->id, $pagoNomina->inicio_periodo, $pagoNomina->fin_periodo);


            $pagoNomina->pagosProvisionesNominaFuncionario()->create([
                'funcionario_id' => $funcionario->id,
                'pago_nomina_id' => $pagoNomina->id,
                'cesantias' =>  $previsiones['resumen']['cesantias']['valor'],
                'intereses_cesantias' =>  $previsiones['resumen']['intereses_cesantias']['valor'],
                'prima' =>  $previsiones['resumen']['prima']['valor'],
                'vacaciones' =>  $previsiones['resumen']['vacaciones']['valor'],
                'dias_acumulados_vacaciones' =>   $previsiones['dias_vacaciones']['vacaciones_acumuladas_periodo'],
                'total_provisiones' =>  $previsiones['valor_total']
            ]);

            $seguridad =  $this->getSeguridad($funcionario->id, $pagoNomina->inicio_periodo, $pagoNomina->fin_periodo);

            $pagoNomina->pagosSeguridadNominaFuncionario()->create([
                'funcionario_id' => $funcionario->id,
                'pago_nomina_id' => $pagoNomina->id,
                'salud' =>  $seguridad['seguridad_social']['Salud'],
                'pension' =>  $seguridad['seguridad_social']['Pensión'],
                'riesgos' =>  $seguridad['seguridad_social']['Riesgos'],
                'sena' => $seguridad['parafiscales']['Sena'],
                'icbf' => $seguridad['parafiscales']['Icbf'],
                'caja_compensacion' => $seguridad['parafiscales']['Caja de compensación'],
                'total_seguridad_social' => $seguridad['valor_total_seguridad'],
                'total_parafiscales' => $seguridad['valor_total_parafiscales'],
                'total_seguridad_social_parafiscales' => $seguridad['valor_total'],
            ]);
        });

        return response()->json(['message' => 'Nómina guardada correctamente', 'Data' =>  $pagoNomina], 200);
    }

    /**
     * Eliminar un Pago de nómina
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        $nomina = PagoNomina::find($id);

        if (!$nomina) {
            return response()->json(['message' => 'Pago de nómina no encontrada'], 404);
        }

        $nomina->delete();

        return response()->json(['message' => 'Pago de nómina eliminada correctamente']);
    }

    public function getPorcentajes()
    {
        return response(NominaSeguridadSocialFuncionario::select('concepto', 'porcentaje')->cursor(), 200);
    }
    public function calculateNeto($dataInfoPago)
    {

        return ($dataInfoPago['valorSalario'] + $dataInfoPago['valorAuxilioTransporte']  + $dataInfoPago['valorHorasExtras'] + $dataInfoPago['valorIngresosAdicionales'] + $dataInfoPago['valorNovedades'])
            -
            ($dataInfoPago['valorRetenciones'] + $dataInfoPago['valorDeducciones']);
    }
}
