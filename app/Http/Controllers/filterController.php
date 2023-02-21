<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use Illuminate\Http\Request;

class filterController extends Controller
{
    public function filterAssignment()
    {
        $query = function ($q) {
            $q->where(function ($q) {
                $q->where('nombres', 'like', '%' . request()->get('nombre') . '%')
                    ->orWhere('apellidos', 'like', '%' . request()->get('nombre') . '%');
            })->where('tipo_turno', 'Rotativo');
        };


        return Dependencia::whereHas('funcionarios',  $query)
            ->with('funcionarios',  $query)
            ->firstWhere('id', request()->get('dependencia'));
    }
}
