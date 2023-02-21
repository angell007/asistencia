<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BancosController extends Controller
{
    public function index()
    {
        return Banco::get(['id', 'nombre']);

    }
}
