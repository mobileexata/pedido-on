<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'empresa_id',
        'nome',
        'documento',
        'iderp',
        'ativo',
        'situacao',
        'saldo_pendente',
        'rota_id',
        'fantasia',
        'dt_nascimento',
        'tp_pessoa',
        'inscricao',
        'cep',
        'numero',
        'logradouro',
        'bairro',
        'cidade',
        'uf',
        'ponto_referencia',
        'email',
        'isento',
        'telefone',
    ];

    protected $casts = [
        'dt_nascimento' => 'date'
    ];
}
