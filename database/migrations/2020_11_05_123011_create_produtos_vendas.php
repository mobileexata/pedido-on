<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutosVendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos_vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendas_id');
            $table->unsignedBigInteger('produtos_id');
            $table->string('nome');
            $table->float('preco')->default(0.00);
            $table->float('quantidade')->default(0.00);
            $table->float('desconto')->default(0.00);
            $table->float('acrescimo')->default(0.00);
            $table->float('total')->default(0.00);
            $table->bigInteger('iderp')->nullable();
            $table->foreign('vendas_id')->on('vendas')->references('id');
            $table->foreign('produtos_id')->on('produtos')->references('id');
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
        Schema::dropIfExists('produtos_vendas');
    }
}
