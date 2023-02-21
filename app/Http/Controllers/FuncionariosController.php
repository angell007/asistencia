<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Http\Requests\FuncionarioRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateFuncionarioRequest;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Funcionario;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

require_once $path = base_path('vendor/pear/http_request2/HTTP/Request2.php');

class FuncionariosController extends Controller
{

    public $ocpApimSubscriptionKey = '';
    public $azure_grupo = '';
    public $uriBase = '';

    public function __construct()
    {


        //$this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getDatos()
    {
        $funcionarios = Funcionario::query();


        $funcionarios->when(request()->get('nameFilter')  != '', function ($q) {
            $q->where(function ($q) {
                $q->where('nombres', 'like', '%' . request()->get('nameFilter') . '%')
                    ->orWhere('apellidos', 'like', '%' . request()->get('nameFilter') . '%');
            });
        })->where('estado',  request()->get('estado'))
            ->with('contractultimate');

        return $funcionarios->orderBy('nombres', 'Asc')->paginate(12);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(FuncionarioRequest $r)
    {

        $empresa = Empresa::where('id', 1)->get();
        $cliente = Cliente::with('face')->where('documento', $empresa[0]["numero_documento"])->get();

        $this->ocpApimSubscriptionKey = $cliente[0]->face->subscription_key;
        $this->azure_grupo = $cliente[0]->face->azure_grupo;
        $this->uriBase = $cliente[0]->face->uri_base;

        $atributos = request()->all();

        $this->ocpApimSubscriptionKey = $cliente[0]->face->subscription_key;
        $this->azure_grupo = $cliente[0]->face->azure_grupo;
        $this->uriBase = $cliente[0]->face->uri_base;

        $atributos = request()->all();

        $img = '';
        if (request()->hasFile('image')) {
            $img = request()->file('image')->store('funcionarios', 's3', 'public');
            $fully =  Storage::disk('s3')->url($img);
        }
        try {
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Han ocurrido errores!',
                'errors' => $e->errors()
            ], 422);
        }

        $atributos["liquidado"] = 0;
        $atributos['image'] = $fully;



        if ($fully  != '') {

            /** CREA EL FUNCIONARIO EN MICROSOFT AZURE */
            $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons');
            $url = $request->getUrl();
            $headers = array(
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            );
            $request->setHeader($headers);
            $parameters = array();
            $body = array(
                "name" => $atributos["nombres"] . " " . $atributos["apellidos"],
                "userData" => $atributos["identidad"]
            );
            $url->setQueryVariables($parameters);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setBody(json_encode($body));
            try {

                $response = $request->send();
                $resp = $response->getBody();
                $resp = json_decode($resp);
                $person_id = $resp->personId;

                $atributos["personId"] = $person_id;
            } catch (HttpException $ex) {
                echo "error: " . $ex;
            }

            /** CREA LOS PUNTOS FACIALES PROPIOS DEL FUNCIONARIO */
            $ruta_guardada = $fully;
            $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $person_id . '/persistedFaces');
            $url = $request->getUrl();

            $headers = array(
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            );

            $request->setHeader($headers);
            $parameters = array(
                "detectionModel" => "detection_02"
            );
            $body = array(
                "url" => $ruta_guardada
            );
            $url->setQueryVariables($parameters);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setBody(json_encode($body));
            try {
                $response = $request->send();
                $resp = $response->getBody();
                $resp = json_decode($resp);

                if (isset($resp->persistedFaceId) && $resp->persistedFaceId != '') {
                    $persistedFaceId = $resp->persistedFaceId;
                    $atributos["persistedFaceId"] = $persistedFaceId;
                } else {

                    if (Storage::disk('s3')->exists($img)) {
                        Storage::disk('s3')->delete($img);
                    }

                    if ($resp->error->code == 'InvalidImage') {
                        return response()->json(['message' => 'No se ha encontrado un rostro en la imagen, revise e intente nuevamente'], 400);
                    }
                    return response()->json(['message' => 'Ha ocurrido un error inisperado'], 400);
                }
            } catch (HttpException $ex) {
                echo $ex;
            }

            /** ENTRENA EL GRUPO PARA QUE IDENTIICQUE EL ROSTRO */

            $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/train');
            $url = $request->getUrl();

            $headers = array(
                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            );
            $request->setHeader($headers);
            $parameters = array();
            $url->setQueryVariables($parameters);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setBody("");
            try {
                $response = $request->send();
                echo $response->getBody();
            } catch (HttpException $ex) {
                echo $ex;
            }
        }


        $funcionario = Funcionario::create($atributos);


        DB::connection('mysql')->table('admins')->insert([
            'usuario' => $funcionario->email,
            'password' => Hash::make($funcionario->identidad),
            'acceso_web' => 1,
            'acceso_app' => 1,
            'funcionario_id' => $funcionario->id,
            'cliente_id' => (auth()->user()) ? (Cliente::find(auth()->user()->cliente_id))->id : 0,
        ]);

        return response()->json(['message' => 'Funcionario creado correctamente'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($identidad)
    {
        $funcionario =  Funcionario::where('identidad', $identidad)
            ->with([
                'pensiones',
                'cajaCompensacion',
                'cesantias',
                'eps',
                'contactosEmergencia',
                'experienciasLaborales',
                'referenciasPersonales',
                'contractultimate',
                'dependencia',
                'turnoFijo',
                'contractultimate.bonificaciones.contableIngreso'
            ])
            ->where('identidad', $identidad)->firstOrFail();

        if (!$funcionario) {
            return response()->json(['message' => 'Funcionario no encontrado'], 404);
        }

        return $funcionario;
    }

    public static function funcionario_turno($personId, $dia, $hoy, $ayer)
    {
        $funcionario =  Funcionario::where('personId', $personId)->with('cargo')->with(['diariosTurnoFijo' => function ($query) use ($hoy) {
            $query->where('fecha', '=', $hoy);
        }])->with(['turnoFijo.horariosTurnoFijo' => function ($query) use ($dia) {
            $query->where('dia', '=', $dia);
        }])->with(['diariosTurnoRotativoAyer' => function ($query) use ($ayer) {
            $query->with('turnoRotativo')->where('fecha', '=', $ayer)->whereNull('fecha_salida');
        }])->with(['diariosTurnoRotativoHoy' => function ($query) use ($hoy) {
            $query->with('turnoRotativo')->where('fecha', '=', $hoy);
        }])->with(
            ['horariosTurnoRotativo' => function ($query) use ($hoy) {
                $query->with('turnoRotativo')->where('fecha', '=', $hoy);
            }]
        )->first();

        if (!$funcionario) {
            return false;
        }
        return $funcionario;
    }
    /**
     * Update the specified resource in storage.
     *
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update($id)
    {

        $empresa = Empresa::where('id', 1)->first();
        $cliente = Cliente::with('face')->where('documento', $empresa["numero_documento"])->get();

        $this->ocpApimSubscriptionKey = $cliente[0]->face->subscription_key;
        $this->azure_grupo = $cliente[0]->face->azure_grupo;
        $this->uriBase = $cliente[0]->face->uri_base;

        if (request()->get('fun')) {
            $atributos = json_decode(request()->get('fun'), true); //sera que es asi?
        } else {
            $atributos = request()->all();
        }

        $funcionario = Funcionario::findOrFail($id);

        $fully = '';
        if (request()->hasFile('image')) {
            if (Storage::disk('s3')->exists($funcionario->image)) {
                Storage::disk('s3')->delete($funcionario->image);
            }
            $img = request()->file('image')->store('funcionarios', 's3', 'public');
            $fully =  Storage::disk('s3')->url($img);
        }

        if ($fully != '') {

            $atributos['image'] = $fully;
            /** SI LA PERSONA NO TIENE PERSON ID SE CREA EL REGISTRO EN MICROSOFT */
            if ($atributos["personId"] == "0" || $atributos["personId"] == "null"  || $atributos["personId"] == null) {
                $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons');
                $url = $request->getUrl();
                $headers = array(
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                );
                $request->setHeader($headers);
                $parameters = array();
                $body = array(
                    "name" => $atributos["nombres"] . " " . $atributos["apellidos"],
                    "userData" => $atributos["identidad"]
                );
                $url->setQueryVariables($parameters);
                $request->setMethod(\HTTP_Request2::METHOD_POST);
                $request->setBody(json_encode($body));
                try {
                    $response = $request->send();
                    $resp = $response->getBody();
                    $resp = json_decode($resp);

                    $atributos["personId"] = $resp->personId;
                } catch (HttpException $ex) {
                    echo "error: " . $ex;
                }
            }
            /** VALIDA SI YA TIENE UN ROSTO LO ELIMINA PARA PODER CREAR EL NUEVO */
            if ($atributos["persistedFaceId"] != "0") {
                $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces/' . $atributos["persistedFaceId"]);
                $url = $request->getUrl();
                $headers = array(
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                );
                $request->setHeader($headers);
                $parameters = array();
                $url->setQueryVariables($parameters);
                $request->setMethod(\HTTP_Request2::METHOD_DELETE);
                $request->setBody("");
                try {
                    $response = $request->send();
                } catch (HttpException $ex) {
                    echo $ex;
                }
            }

            /** CREA LOS PUNTOS FACIALES PROPIOS DEL FUNCIONARIO */
            $ruta_guardada = $fully;
            $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces');
            $url = $request->getUrl();

            $headers = array(
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            );

            $request->setHeader($headers);
            $parameters = array(
                "detectionModel" => "detection_02"
            );
            $body = array(
                "url" => $ruta_guardada
            );
            $url->setQueryVariables($parameters);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setBody(json_encode($body));

            try {
                $response = $request->send();
                $resp = $response->getBody();
                $resp = json_decode($resp);

                if (isset($resp->persistedFaceId) && $resp->persistedFaceId != '') {
                    $persistedFaceId = $resp->persistedFaceId;
                    $atributos["persistedFaceId"] = $persistedFaceId;
                } else {
                    if (Storage::disk('s3')->exists($img)) {
                        Storage::disk('s3')->delete($img);
                    }
                    if ($resp->error->code == 'InvalidImage') {
                        return response()->json(['message' => 'No se ha encontrado un rostro en la imagen, revise e intente nuevamente'], 400);
                    }
                    return response()->json(['message' => 'Ha ocurrido un error inisperado'], 400);
                }
            } catch (HttpException $ex) {
                echo $ex;
            }

            /** ENTRENA EL GRUPO PARA QUE IDENTIICQUE EL ROSTRO */
            $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/train');
            $url = $request->getUrl();

            $headers = array(
                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            );
            $request->setHeader($headers);
            $parameters = array();
            $url->setQueryVariables($parameters);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setBody("");
            try {
                $response = $request->send();
                echo $response->getBody();
            } catch (HttpException $ex) {
                echo $ex;
            }
        }

        $funcionario->update($atributos);
        $data = $funcionario->load('contractultimate');
        return response()->json(['message' => 'Datos del funcionario actualizado correctamente', 'imagen' => $fully, 'Funcionario' => $data]);
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

    public function resetPassword()
    {
        DB::getDefaultConnection();
        $user = Admin::with("cliente")->find(auth()->user()->id);
        $user->password = Hash::make(request()->get('password'));
        $user->save();

        $ruta = $user["cliente"]["ruta"];
        Config::set("database.connections.Tenantcy.database", 'geneticapp-cliente_' . $ruta);
        $funcionario =  DB::connection('Tenantcy')->table('funcionario')->where('id', $user['funcionario_id'])->get(["nombres", "apellidos", "identidad", "image"]);
        $funcionario[0]->brand  = false;

        return response()->json(['message' => 'Contraseña actualizada correctamente', 'data' => $funcionario], 200);
    }
}
