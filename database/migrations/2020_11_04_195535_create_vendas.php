<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresas_id');
            $table->unsignedBigInteger('clientes_id');
            $table->unsignedBigInteger('tiposvendas_id');
            $table->unsignedBigInteger('users_id');
            $table->text('observacoes')->nullable();
            $table->float('total')->default(0.00);
            $table->float('desconto')->default(0.00);
            $table->float('acrescimo')->default(0.00);
            $table->enum('cancelada', ['S', 'N'])->default('N');
            $table->enum('concluida', ['S', 'N'])->default('N');
            $table->bigInteger('iderp')->nullable();
            $table->foreign('empresas_id')->on('empresas')->references('id');
            $table->foreign('clientes_id')->on('clientes')->references('id');
            $table->foreign('tiposvendas_id')->on('tiposvendas')->references('id');
            $table->foreign('users_id')->on('users')->references('id');
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
        Schema::dropIfExists('vendas');
    }
}
