<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{

    protected $fillable = [
        'empresa_id', 'nome', 'iderp'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
