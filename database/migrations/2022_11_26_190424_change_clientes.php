<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('fantasia')->nullable();
            $table->date('dt_nascimento')->nullable();
            $table->enum('tp_pessoa', ['F', 'J']);
            $table->string('inscricao')->nullable();
            $table->string('cep')->nullable();
            $table->string('numero')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->char('uf', 2)->nullable();
            $table->string('ponto_referencia')->nullable();
            $table->string('email')->nullable();
            $table->enum('isento', ['S', 'N'])->nullable();
            $table->string('telefone')->nullable();
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
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('fantasia');
            $table->dropColumn('dt_nascimento');
            $table->dropColumn('tp_pessoa');
            $table->dropColumn('inscricao');
            $table->dropColumn('cep');
            $table->dropColumn('numero');
            $table->dropColumn('logradouro');
            $table->dropColumn('bairro');
            $table->dropColumn('cidade');
            $table->dropColumn('uf');
            $table->dropColumn('ponto_referencia');
            $table->dropColumn('email');
            $table->dropColumn('isento');
            $table->dropColumn('telefone');
        });
    }
}
