<?php

namespace App\Http\Controllers;

use App\Mail\Preaviso;
use App\Models\Funcionario;
use App\Models\Preaviso as ModelsPreaviso;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class MailController extends Controller
{

    public function preaviso()
    {
        $funcionario = Funcionario::with('cargo')->with(['contractultimate' => function ($q) {
            $q->whereNotNull('fecha_fin')->whereBetween('fecha_fin', [Carbon::now()->addDays(30), Carbon::now()->addDays(45)])->orderBy('id', 'Desc')->take(1);
        }])->findOrFail(request()->get('id'));



        // Mail::to($funcionario->email)->send(new  Preaviso($funcionario));
        // if (Mail::failures()) {
        //     return response()->json('Fallos en envio');
        // } else {
        ModelsPreaviso::create([
            'fecha_envio' => Carbon::now()->format('Y-m-d'),
            'funcionario_id' =>  $funcionario->id,
            'fecha_inicio_contrato' => $funcionario->contractultimate->fecha_inicio,
            'fecha_fin_contrato' =>  $funcionario->contractultimate->fecha_fin,
            'meses_duracion' =>  ceil(Carbon::parse($funcionario->contractultimate->fecha_inicio)->floatDiffInMonths(Carbon::parse($funcionario->contractultimate->fecha_fin))),
            'administrador_id' => auth()->user()->funcionario_id
        ]);


        // }
    }
    public function OtroSi()
    {
        # code...
    }
}
