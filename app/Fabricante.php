<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fabricante extends Model
{

    protected $fillable = [
        'empresa_id', 'nome', 'iderp'
    ];

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

}
