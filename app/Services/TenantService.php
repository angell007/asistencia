<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Face;
use App\Models\Funcionario;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class TenantService
{
    public function createCliente($data, $ruta)
    {
        /** Creo el Cliente dentro de mi base de datos para controlar todo acerca de él */
        $ruta =  md5(Str::camel($data['empresa'])); // Esta es la ruta cifrada y nombre de la base de datos tambien
        $face =   Face::firstWhere('estado', 'activo');

        $cliente = Cliente::create([
            'nombre' => $data['empresa'],
            'dv' => $data['dv'],
            'documento' => $data['nit'],
            'digito_verificacion' => $data['dv'],
            'correo_registrado' => $data['usuario'],
            'pais_id' => $data['pais'],
            'tipo_negocio' => $data['tipo_negocio'],
            'valor_contrato' => $data['valor_contrato'],
            'fecha_creacion' => date("Y-m-d H:m:s"),
            'fecha_renovacion' => date("Y-m-d H:m:s"),
            'fecha_vencimiento' => date("2200-m-d H:m:s"),
            'tipo_pago' => $data['tipo_pago'],
            'ruta' => $ruta,
            'terminos' => $data['terminos'],
            'sector' => $data['sector'],
            'empleados' => $data['empleados'],
            'intereses' => $data['intereses'],
            'face_id' => $face->id,
        ]);

        $face->update(['estado' => 'inactivo']);
        return $cliente;
    }

    public function createAdmin($data)
    {
        /** Creo el usuario que va a realizar login desde mi BD principal*/
        $admin = Admin::create([
            'usuario' => $data['usuario'],
            'password' => Hash::make($data['password']),
            'acceso_app' => 1,
            'acceso_web' => 1,
        ]);

        return $admin;
    }

    public function createTenant($ruta)
    {
        /** Creo el TENANT donde se alojará la información del Cliente */
        $tenant = Tenant::create(['id' => $ruta]);
        $tenant->domains()->create(['domain' => $ruta]);
    }

    public function createEmpresa($data)
    {

        $config = Configuracion::firstWhere('pais', $data['pais']);

        /** Guardo datos inciales de empresa */
        Empresa::create([
            'razon_social' => $data['empresa'],
            'tipo_documento' => $data['tipo_documento'],
            'numero_documento' => $data['nit'],
            'digito_verificacion' => $data['dv'],
            'email_contacto' => $data['usuario'],
            'salario_base' => $config->salario_base,
            'auxilio_transporte' => $config->auxilio_transporte,
            'hora_inicio_noche' => '21:00',
            'hora_fin_noche' => '06:00',
            'frecuencia_pago' => 'Mensual',
            'banco_id' => 1,
            'tipo_cuenta' => 1,
            'numero_cuenta' => 12345678910,
            'operador_pago' => 1,
            'medio_pago' => 1,
            'arl_id' => 1,
        ]);
    }
    public function createFuncionario($data)
    {

        $config = Configuracion::firstWhere('pais', $data['pais']);

        /** Guardo datos iniciales de funcionarios */

        $funcionario =   Funcionario::create([
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'identidad' => $data['cedula'],
            'email' => $data['usuario'],
            'fecha_retiro' => date("2200-m-d H:m:s"),
            /********************************************************** */
            'eps_id' => 1,
            'cesantias_id' => 1,
            'pensiones_id' => 1,
            'caja_compensacion_id' => 1,
            'tipo_contrato_id' => 1,
            'nomina_riesgos_arl_id' => 1,
            'jefe_id' => 1,
            'dependencia_id' => 1,
            'cargo_id' => 1,
            'estado_civil' => 'Soltero(a)',
            'salario' => $config->salario_base,
            'tipo_turno' => 'Fijo',
            /********************************************************** */
            'talla_pantalon' => '36',
            'talla_botas' => '44',
            'talla_camisa' => '44',
            /********************************************************** */
            'sexo' => $data["genero"],
            'liquidado' => 0,
            'fecha_nacimiento' => '1980-01-01',
            'fecha_ingreso' => Carbon::now()->format('Y-m-d'),
            'tipo_sangre' => 'O+',
            'turno_fijo_id' => 1,
        ]);

        return $funcionario;
    }
}
