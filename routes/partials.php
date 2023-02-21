<?php

use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\PagosNominaController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

Route::group([
    'middleware' => [InitializeTenancyByPath::class],
    'prefix' => '/{tenant}',
], function () {
    Route::get('/get-nomina-seguridad-funcionario', [PagosNominaController::class, 'getPorcentajes']);
    Route::get('/configuracion/get-porcentajes-incapacidades', [ConfiguracionController::class, 'getPorcentajesIncapacidades']);
});
