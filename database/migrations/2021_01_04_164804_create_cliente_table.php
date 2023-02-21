<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('face_id')->nullable();
            $table->string('documento', 100)->nullable();
            $table->string('dv', 2)->nullable();
            $table->string('nombre', 200)->nullable();
            $table->integer('empleados_activos')->nullable();
            $table->string('tipo_negocio', 100)->nullable();
            $table->integer('valor_contrato')->nullable();
            $table->integer('total_mes')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_renovacion')->nullable();
            $table->string('tipo_pago', 100)->nullable();
            $table->string('estado')->nullable();
            $table->string('ruta')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->timestamps();
            $table->integer('pais_id')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('correo_registrado', 100)->nullable();
            $table->string('subscriptionKey', 200)->nullable();
            $table->string('azureGrupo', 200)->nullable();
            $table->string('uriBase', 200)->nullable();
            $table->string('terminos', 100)->nullable();
            $table->string('sector', 100)->nullable();
            $table->string('empleados', 100)->nullable();
            $table->text('intereses')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliente');
    }
}
