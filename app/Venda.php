<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{

    protected $fillable = [
        'empresa_id', 'cliente_id', 'tiposvenda_id', 'user_id', 'owner_user_id', 'observacoes', 'total', 'desconto', 'acrescimo', 'cancelada', 'concluida', 'iderp'
    ];

    public function empresa()
    {
        return $this->hasMany(Empresa::class, 'id', 'empresa_id');
    }

    public  function cliente()
    {
        return $this->hasMany(Cliente::class, 'id', 'cliente_id');
    }

    public function tipoVenda()
    {
        return $this->hasMany(TiposVenda::class, 'id', 'tiposvenda_id');
    }

    public function produtos()
    {
        return $this->hasMany(ProdutoVenda::class);
    }

    public function vendedor()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function deletar()
    {
        foreach ($this->produtos()->get() as $p) {
            $p->deletar();
        }
        return $this->delete();
    }

    public function calculaTotal()
    {
        $produtos = $this->produtos();
        $this->total = (float)$produtos->sum('total') + $this->acrescimo - $this->desconto;
        $this->save();
        return true;
    }

}
