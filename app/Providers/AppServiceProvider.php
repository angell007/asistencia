<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\PagoProvisionesNominaFuncionario;
use App\Observers\ClienteObserver;
use App\Observers\ContratoObserver;
use App\Observers\EmpresaObserver;
use App\Observers\FuncionarioObserver;
use App\Observers\PagoProvisionesNominaFuncionarioObserver;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {


        Response::macro('success', function ($message) {
            return ['success' => true, 'message' => $message];
        });

        Response::macro('fail', function ($message) {
            return ['success' => false, 'message' => $message];
        });


        Funcionario::observe(FuncionarioObserver::class);
        Cliente::observe(ClienteObserver::class);
        Empresa::observe(EmpresaObserver::class);
        PagoProvisionesNominaFuncionario::observe(PagoProvisionesNominaFuncionarioObserver::class);
        Contrato::observe(ContratoObserver::class);

        Schema::defaultStringLength(191);

        //formato de moneda para el reporte pdf de la colilla y otros reportes de nómina
        Blade::directive('money', function ($money) {
            return "<?php echo '$' . number_format($money, 2); ?>";
        });

        //Settear a Carbo en español
        Carbon::setLocale(config('app.locale'));
        date_default_timezone_set('America/Bogota');
        CarbonInterval::setCascadeFactors(array(
            'days' => array(24, 'hours'),
            'weeks' => array(7, 'days'),
            'months' => array(30, 'days'),
        ));
    }
}
