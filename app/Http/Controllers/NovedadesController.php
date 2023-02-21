<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestNovedadesCreate;
use App\Models\Funcionario;
use App\Models\Novedad;
use App\Models\ContableLicenciaIncapacidad;
use App\Traits\validateDates;

class NovedadesController extends Controller
{
    use validateDates;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('novedades.index');
    }

    public function getDatos($fechaInicio, $fechaFin)
    {

        return Funcionario::select(['id', 'nombres', 'apellidos', 'image', 'dependencia_id'])->with('dependencia:id,nombre')
            ->whereHas('novedades', function ($query) use ($fechaInicio, $fechaFin) {
                $query->with("novedad")->where('fecha_inicio', '>=', $fechaInicio)->where('fecha_fin', '<=', $fechaFin);
            })->with(["novedades" => function ($query) use ($fechaInicio, $fechaFin) {
                $query->with("novedad")->where('fecha_inicio', '>=', $fechaInicio)->where('fecha_fin', '<=', $fechaFin);
            }])->orderBy('nombres', 'Asc')->get();
    }


    public function getDatos2($fechaInicio, $fechaFin)
    {
        return Novedad::with(['funcionario' => function ($query) {
            $query->select(['id', 'nombres', 'apellidos', 'image', 'dependencia_id'])->with('dependencia:id,nombre');
        }])->with('novedad')->where('fecha_inicio', '>=', $fechaInicio)
            ->where('fecha_fin', '<=', $fechaFin)->get();
    }

    public function getFuncionarios()
    {
        return Funcionario::get(['id', 'nombres', 'apellidos', 'dependencia_id']);
    }

    public function getNovedades()
    {
        return ContableLicenciaIncapacidad::get(['id', 'concepto', 'suma']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RequestNovedadesCreate $request)
    {

        if ($this->customValidate($request->all())) {
            Novedad::create($this->pushFlag($request->all()));
            return response()->success('Novedad creada correctamente');
        }
        return response()->fail('El funcionario ya se encuentra con novedades registradas en este periodo');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $novedad = Novedad::findOrFail($id);

        $atributos = request()->validate([
            'funcionario_id' => 'required',
            'contable_licencia_incapacidad_id' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'tipo' => 'required',
            'modalidad' => 'required',
            'observacion' => '',
        ], [
            'funcionario_id.required' => 'El campo de funcionario es obligatorio',
            'contable_licencia_incapacidad_id.required' => 'El campo de novedad es obligatorio'
        ]);

        $novedad->update($atributos);

        return response()->json(['message' => 'Novedad actualizada correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $novedad = Novedad::findOrFail($id);

        $novedad->delete();

        return response()->json(['message' => 'Novedad eliminada correctamente']);
    }

    /***
     * Se incluye el campo suma basado en el campo suma de la licencia.
     *  @param array $request from request user 
     *  @return arrray $request 
     */
    public function pushFlag(array $request): array
    {
        $request['suma'] = (ContableLicenciaIncapacidad::find($request['contable_licencia_incapacidad_id'], ['suma']))->suma;
        return  $request;
    }
}
