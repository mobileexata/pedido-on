<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGruposSubGrupos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->json('grupo');
        });
        DB::update("update produtos set grupo = '{\"codgrupo\":1,\"descgrupo\":\"GERAL\",\"codsubgrupo\":1,\"descsubgrupo\":\"GERAL\"}'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('grupo');
        });
    }
}
