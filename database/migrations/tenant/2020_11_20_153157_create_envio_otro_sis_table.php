<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnvioOtroSisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('envio_otro_sis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fecha_envio');
            $table->unsignedBigInteger('funcionario_id');
            $table->string('fecha_inicio_contrato');
            $table->string('fecha_fin_contrato');
            $table->string('meses_duracion');
            $table->unsignedBigInteger('administrador_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('envio_otro_sis');
    }
}
