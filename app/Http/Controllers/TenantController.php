<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Cliente;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseConnection;
use App\Services\TenantService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;

class TenantController extends Controller
{

    private $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
        DB::getDefaultConnection();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {

            $data = request()->all();
            $ruta =  md5(Str::camel($data['empresa']));

            $cliente = $this->tenantService->createCliente($data, $ruta);
            $admin = $this->tenantService->createAdmin($data);
            $this->tenantService->createTenant($ruta);
            Config::set("database.connections.Tenantcy.database", 'geneticapp-cliente_' . $ruta);
            $this->tenantService->createEmpresa($data);
            $funcionario = $this->tenantService->createFuncionario($data);

            $admin->funcionario_id = $funcionario->id;
            $admin->cliente_id = $cliente->id;
            $admin->save();

            DB::commit();
            return response()->json(['data' => 'Cliente Creado Exitosamente']);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::critical($th->getMessage());
            return response()->json($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
