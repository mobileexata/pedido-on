<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    protected $fillable = [
        'razao', 'fantasia', 'cnpj', 'iderp', 'user_id'
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    public function tiposVendas()
    {
        return $this->hasMany(TiposVenda::class);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function rotas()
    {
        return $this->hasMany(Rota::class);
    }

}
