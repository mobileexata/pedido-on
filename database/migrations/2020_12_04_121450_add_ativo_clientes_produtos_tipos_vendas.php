<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtivoClientesProdutosTiposVendas extends Migration
{
    private $tables = ['clientes', 'tiposvendas', 'produtos'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $t)
            Schema::table($t, function (Blueprint $table) {
                $table->enum('ativo', ['S', 'N'])->default('S');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $t)
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('ativo');
            });
    }
}
