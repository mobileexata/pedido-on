<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTiposVendasChangeForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tiposvendas', function (Blueprint $table) {
            $table->renameColumn('empresas_id', 'empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tiposvendas', function (Blueprint $table) {
            $table->renameColumn('empresa_id', 'empresas_id');
        });
    }
}
