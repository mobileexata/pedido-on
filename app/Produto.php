<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{

    protected $fillable = [
        'empresa_id', 'nome', 'preco', 'estoque', 'iderp', 'imagem', 'ativo', 'ean', 'referencia', 'fabricante_id', 'precos'
    ];

    protected $casts = [
        'precos' => 'array',
    ];

    public function atualizaEstoque($qtd, $op = '-')
    {
        if ($op == '-')
            $this->estoque -= $qtd;
        else
            $this->estoque += $qtd;
        $this->save();
    }

    public function getPrecoVenda($idErpTipoVenda = 0) {
        if ($idErpTipoVenda == 0) {
            return $this->preco;
        }
        return $this->precos[$idErpTipoVenda];
    }

    public function getImageAttribute()
    {
        return $this->imagem ?  public_path('produtos/' . $this->imagem) : public_path('images/no_photo.png');
    }
}
