<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'empresa_id', 'nome', 'documento', 'iderp', 'ativo', 'situacao', 'saldo_pendente', 'rota_id'
    ];
}
