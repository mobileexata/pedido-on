<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiposVenda extends Model
{
    protected $table = 'tiposvendas';

    protected $fillable = [
        'empresa_id', 'nome', 'iderp', 'ativo', 'idtipoprecoerp', 'desctipopreco'
    ];
}
