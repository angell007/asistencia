<?php

namespace App\Http\Controllers;

use App\Models\ContableIngreso;
use Illuminate\Http\Request;

class ContableIngresoController extends Controller
{
    public function index()
    {
        return ContableIngreso::all();
    }
}
