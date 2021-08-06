<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProdutosVendasChangeForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtos_vendas', function (Blueprint $table) {
            $table->renameColumn('vendas_id', 'venda_id');
            $table->renameColumn('produtos_id', 'produto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtos_vendas', function (Blueprint $table) {
            $table->renameColumn('venda_id', 'vendas_id');
            $table->renameColumn('produto_id', 'produtos_id');
        });
    }
}
