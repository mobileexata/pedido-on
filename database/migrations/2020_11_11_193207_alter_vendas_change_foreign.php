<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVendasChangeForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->renameColumn('empresas_id', 'empresa_id');
            $table->renameColumn('clientes_id', 'cliente_id');
            $table->renameColumn('tiposvendas_id', 'tiposvenda_id');
            $table->renameColumn('users_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->renameColumn('empresa_id', 'empresas_id');
            $table->renameColumn('cliente_id', 'clientes_id');
            $table->renameColumn('tiposvenda_id', 'tiposvendas_id');
            $table->renameColumn('user_id', 'users_id');
        });
    }
}
