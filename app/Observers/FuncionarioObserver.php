<?php

namespace App\Observers;

// use App\Models\Admin;

// use App\Admin;

use App\Models\Admin;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\subMenu;
use App\Models\TipoContrato;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FuncionarioObserver
{
    /**
     * Handle the funcionario "created" event.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return void
     */
    public function created(Funcionario $funcionario)
    {
        $subsidio_transporte = 1;

        $submenus = subMenu::all();

        if (auth()->user()) {
            Config::set("database.connections.Tenantcy.database", 'geneticapp-cliente_' . (Cliente::find(auth()->user()->cliente_id))->ruta);
        }

        foreach ($submenus  as  $submenu) {
            DB::connection('Tenantcy')->table('funcionario_submenu')->insert([
                [
                    'funcionario_id' => $funcionario->id,
                    'submenu_id' => $submenu->id
                ],
            ]);
        }

        $empresa = Empresa::where('id', 1)->get();
        if ((int)$funcionario->salario > (int)$empresa[0]['salario_base'] * 2) {
            $subsidio_transporte = 0;
        }

        Contrato::create([
            'subsidio_transporte' => $subsidio_transporte,
            'funcionario_id' => $funcionario->id,
            'fecha_inicio' => (request()->get('fecha_ingreso') != '') ? request()->get('fecha_ingreso') : Carbon::now()->format('Y-m-d'),
            'fecha_fin' => (request()->get('fecha_retiro') != '') ? request()->get('fecha_retiro') : date("2200-m-d H:m:s"),
            'salario' => (request()->get('salario') != '') ? request()->get('salario') :  $empresa[0]['salario_base'],
            'centro_costo_id' => (request()->get('centro_costo_id') != '') ?  request()->get('centro_costo_id') : 0,
            'dependencia_id' => (request()->get('dependencia_id') != '') ?  request()->get('dependencia_id') : 1,
            'cargo_id' => (request()->get('cargo_id') != '') ? request()->get('cargo_id') : 1,
            'tipo_contrato_id' => (request()->get('tipo_contrato_id') != '') ? request()->get('tipo_contrato_id') : 1,
            'liquidado' => (request()->get('liquidado') != '') ?  request()->get('liquidado') : 0,
            'turno_fijo_id' => (request()->get('turno_fijo_id') != '') ?  request()->get('turno_fijo_id') : 1,
            'jefe_id' => (request()->get('jefe_id') != '') ?  request()->get('jefe_id') : 0,
        ]);
    }

    /**
     * Handle the funcionario "updated" event.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return void
     */
    public function saved(Funcionario $funcionario)
    {

        if (!$funcionario->wasRecentlyCreated) {

            $contrato = Contrato::where('funcionario_id', $funcionario->id)->get()->last();

            if (request()->get('fun')) {
                $dataContract = json_decode(request()->get('fun'), true); //sera que es asi?
            } else {
                $dataContract = request()->all();
            }

            $contractRequest = TipoContrato::find($dataContract['contractultimate']['tipo_contrato']['id']);

            if (!$contractRequest->terminacion) {
                $contrato->fecha_fin = null;

                $empresa = Empresa::where('id', 1)->get();
                if ((int)$dataContract['contractultimate']['salario'] > (int)$empresa[0]['salario_base'] * 2) {
                    $contrato->subsidio_transporte = 0;
                }

                $contrato->update([
                    'subsidio_transporte' => ($dataContract['contractultimate']['subsidio_transporte'] != '') ? $dataContract['contractultimate']['subsidio_transporte']  :  $contrato->subsidio_transporte,
                    'centro_costo_id' => ($dataContract['contractultimate']['centro_costo_id'] != '') ?  $dataContract['contractultimate']['centro_costo_id'] :  $contrato->centro_costo_id,
                    'dependencia_id' => ($dataContract['contractultimate']['dependencia_id'] != '') ?  $dataContract['contractultimate']['dependencia_id'] :  $contrato->dependencia_id,
                    'turno_fijo_id' => ($dataContract['contractultimate']['turno_fijo_id'] != '') ?  $dataContract['contractultimate']['turno_fijo_id'] :  $contrato->turno_fijo_id,
                    'jefe_id' => ($dataContract['contractultimate']['jefe_id'] != '') ?  $dataContract['contractultimate']['jefe_id'] :  $contrato->jefe_id,
                    'fecha_inicio' => ($dataContract['contractultimate']['fecha_inicio'] != '') ? $dataContract['contractultimate']['fecha_inicio'] : $contrato->fecha_inicio,
                    'fecha_fin' => ($dataContract['contractultimate']['fecha_fin'] != '') ? $dataContract['contractultimate']['fecha_fin'] :  $contrato->fecha_fin,
                    'salario' => ($dataContract['contractultimate']['salario'] != '') ? $dataContract['contractultimate']['salario'] :  $contrato->salario,
                    'cargo_id' => ($dataContract['contractultimate']['cargo_id'] != '') ? $dataContract['contractultimate']['cargo_id'] :  $contrato->cargo_id,
                    'tipo_contrato_id' => ($dataContract['contractultimate']['tipo_contrato']['id'] != '') ? $dataContract['contractultimate']['tipo_contrato']['id'] : $contrato->tipo_contrato_id,
                    'liquidado' => ($dataContract['contractultimate']['liquidado'] != '') ?  $dataContract['contractultimate']['liquidado'] : $contrato->liquidado,
                ]);
            }
        }
    }

    /**
     * Handle the funcionario "deleted" event.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return void
     */
    public function deleted(Funcionario $funcionario)
    {
        //
    }

    /**
     * Handle the funcionario "restored" event.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return void
     */
    public function restored(Funcionario $funcionario)
    {
        //
    }

    /**
     * Handle the funcionario "force deleted" event.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return void
     */
    public function forceDeleted(Funcionario $funcionario)
    {
        //
    }
}
