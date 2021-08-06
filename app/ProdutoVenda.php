<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdutoVenda extends Model
{
    protected $table = 'produto_venda';

    protected $fillable = [
        'venda_id', 'produto_id', 'nome', 'preco', 'quantidade', 'desconto', 'acrescimo', 'total', 'iderp'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function atualizaEstoqueCadastro()
    {
        $this->produto()->first()->atualizaEstoque($this->quantidade);
    }

    public function deletar()
    {
        $this->produto()->first()->atualizaEstoque($this->quantidade, '+');
        $this->delete();
    }
}
