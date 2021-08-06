<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIderpAllTables extends Migration
{
    private $empresas = ['empresas', 'clientes', 'tiposvendas', 'produtos', 'vendas'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->empresas as $e) {
            Schema::table($e, function (Blueprint $table) {
                $table->string('iderp')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->empresas as $e) {
            Schema::table($e, function (Blueprint $table) {
                $table->bigInteger('iderp')->change();
            });
        }
    }
}
