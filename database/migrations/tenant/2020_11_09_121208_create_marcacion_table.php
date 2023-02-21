<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarcacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marcacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo');
            $table->string('detalles');
            $table->string('fecha');
            $table->timestamps();
            $table->string('img', 250)->nullable();
            $table->integer('funcionario_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marcacion');
    }
}
