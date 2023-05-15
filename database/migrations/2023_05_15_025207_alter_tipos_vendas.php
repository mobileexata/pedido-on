<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTiposVendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tiposvendas', function (Blueprint $table) {
            $table->bigInteger('idtipoprecoerp');
            $table->string('desctipopreco');
        });
        DB::update("update tiposvendas set idtipoprecoerp = 1, desctipopreco = 'À VISTA'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tiposvendas', function (Blueprint $table) {
            $table->dropColumn(['idtipoprecoerp', 'desctipopreco']);
        });
    }
}
