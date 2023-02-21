<?php

namespace Database\Seeders;

use App\Models\PorcentageIncapacidad;
use Illuminate\Database\Seeder;

class PorcentageIncapacidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PorcentageIncapacidad::create([
            'nombre' => 'del 100 % al 66,67%',
            'valor' => 'Al 66,66 del dia uno al 90',
        ]);

        PorcentageIncapacidad::create([
            'nombre' => 'Al 100% del dia uno al 90',
            'valor' => 'Al 100% del dia uno al 90',
        ]);

        PorcentageIncapacidad::create([
            'nombre' => 'Al 100 % primeros 2 dias y desde el dia 3 al 66,67%',
            'valor' => 'Al 100 % primeros 2 dias y desde el dia 3 al 66,67%',
        ]);
    }
}
