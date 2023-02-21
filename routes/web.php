<?php

use App\Admin;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PaginaPrincipalController;
use App\Http\Controllers\PagosNominaController;
use App\Http\Controllers\TenantController;
use App\Http\Libs\Nomina\Calculos\CalculoNovedades;
use App\Http\Libs\Nomina\Calculos\CalculoSalario;
// use App\Mail\RecoveryMail;
use App\Models\CajaCompensacion;
use App\Models\CentroCosto;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Correo;
use App\Models\Empresa;
use App\Models\EmpresaConfiguracion;
use App\Models\Face;
use App\Models\Formato;
use App\Models\Funcionario;
use App\Models\FuncionarioDocumento;
use App\Models\NominaSeguridadSocialFuncionario;
use App\Models\PagoNomina;
use App\Models\ReporteExtras;
use App\Models\TurnoFijo;
use App\Models\TurnoRotativo;
use Aws\Route53\Exception\Route53Exception;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Illuminate\Support\Str;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function PHPSTORM_META\type;

// mytextarea

Route::get('/test', function () {

   
   $date = Carbon::now()->format('Ymd');
   $doc = 91520702;
   $base64 = base64_encode("4,$doc");
   $base64url = strtr($base64, '+/=', '-_ ');
   $url = 'http://genetitc20.test/ae/' . $base64url;

   return $url;
   // return Hash::make('secret');
});

Route::get('/auth/pass-reset',  function () {


   
   Log::critical('Funciona');

   // $user = Admin::with("cliente")->firstWhere('usuario', request()->get('email'));

   // $ruta = $user["cliente"]["ruta"];
   // Config::set("database.connections.Tenantcy.database", 'geneticapp-cliente_' . $ruta);
   // $funcionario =  DB::connection('Tenantcy')->table('funcionario')->find($user['funcionario_id']);

   // $user->password = Hash::make($funcionario->identidad);
   // $user->save();

   // Mail::to($user->usuario)->send(new RecoveryMail($funcionario));
});

Route::group([
   'middleware' => [InitializeTenancyByPath::class],
   'prefix' => '/{tenant}',
], function () {

   Route::get('/administracion', function () {

      
      $empresa = Empresa::first();
      $funcionario = Funcionario::first();

      $formato = Formato::firstWhere('tipo_formato_id', 1);

      $formato->cuerpo =  preg_replace("/@nombre_empresa@/",  $empresa->razon_social, $formato->cuerpo);
      $formato->cuerpo =  preg_replace("/@nombre_funcionario@/",  $funcionario->nombres .' '. $funcionario->apellidos , $formato->cuerpo);


         $pdf = \App::make('dompdf.wrapper');
         $pdf->setPaper("A4", "portrait");
         $pdf->loadView( 'mails.preaviso' , compact('formato', 'empresa') );
      return $pdf->stream();


      // return view('mails.preaviso', compact('formato'));


      // $cascades = CarbonInterval::getCascadeFactors(); // save initial factors

      // CarbonInterval::setCascadeFactors([
      //    'minute' => [60, 'seconds'],
      //    'hour' => [60, 'minutes'],
      //    'day' => [8, 'hours'],
      //    'week' => [5, 'days'],
      //    // in this example the cascade won't go farther than week unit
      // ]);

      // echo CarbonInterval::fromString('20h')->cascade()->forHumans();
      // echo '<br/>';            // 2 days 4 hours
      // echo CarbonInterval::fromString('10d')->cascade()->forHumans();
      // echo '<br/>';           // 2 weeks
      // echo CarbonInterval::fromString('3w 18d 53h 159m')->cascade()->forHumans();
      // // 7 weeks 4 days 7 hours 39 minutes
      // echo '<br/>';
      // // You can see currently set factors with getFactor:
      // echo CarbonInterval::getFactor('minutes', /* per */ 'hour');
      // echo '<br/>';               // 60
      // echo CarbonInterval::getFactor('days', 'week');
      // // 5
      // echo '<br/>';
      // // And common factors can be get with short-cut methods:
      // echo CarbonInterval::getDaysPerMonth();
      echo '<br/>';                                      // 5
      // echo CarbonInterval::getHoursPerDay();
      // echo '<br/>';                                  // 8
      // echo CarbonInterval::getMinutesPerHour();
      // echo '<br/>';                               // 60
      // echo CarbonInterval::getSecondsPerMinute();
      // echo '<br/>';                               // 60
      // echo CarbonInterval::getMillisecondsPerSecond();
      // echo '<br/>';                            // 1000
      // echo CarbonInterval::getMicrosecondsPerMillisecond();                        // 1000

















      // dump($horariosExistentes);

      // return TurnoRotativo::all();



      // $period = CarbonPeriod::between($nomina->inicio_periodo, $nomina->fin_periodo)->filter('isToday');

      // $brand = false;

      // $period =  CarbonPeriod::between($nomina->inicio_periodo, $nomina->fin_periodo);

      // $isperiodo = function ($date) use ($brand){
      //    if ($date->isToday()) {
      //        $brand = true;
      //    }
      // };
      // $period->filter($isperiodo);
      // return   $period;


      // if( CarbonPeriod::between($nomina->inicio_periodo, $nomina->fin_periodo) ){

      // }

      //where(
      //        function ($q)
      //       {
      //        $q->whereBetween('enrolled', ['2020-06-01 00:00:00', '2020-0-28 00:00:00']);
      //       //  $q->whereBetween('enrolled', ['2020-06-01 00:00:00', '2020-0-28 00:00:00']);
      //       }
      // )->first();


      // return EmpresaConfiguracion::with('procentaje:id,valor')->exclude(['created_at', 'updated_at'])->get();

      // header("Content-Type: text/plain");
      // header('Content-Disposition: attachment; filename="default-filename.txt"');
      // echo "Hola mundo xxxxzx";
      // echo "Hola mundo xxxxzx";
      // echo "</br>";
      // echo "Hola mundo xxxxzx";
      // echo "Hola mundo xxxxzx";
      // echo "\r\n";
      // echo "\r\n";
      // echo "\r\n";
      // echo "Hola mundo xxxxzx";

   });
});

Route::post('/getTenant', function () {
   if (request()->wantsJson()) {
      if (request()->get('nit') != null) {
         $empresa =  Cliente::where('documento', request()->get('nit'))->first();
         return response()->json($empresa->ruta);
      }
   }
});

Route::resource('tenants', TenantController::class);
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('/{excepcion}', [PaginaPrincipalController::class, 'index'])->where('excepcion', '^(?!api\/)[\/\w\.-]*');
