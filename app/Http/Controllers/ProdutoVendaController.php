<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoVendaRequest;
use App\ProdutoVenda;
use App\Venda;
use Illuminate\Http\Request;

class ProdutoVendaController extends Controller
{

    public function index()
    {
        $data = request()->all();
        $venda = $data['produtos_venda'] ?? 0;
        $v = Venda::findOrFail($venda);
        return view('vendas.produtos.index', ['produtos' => $v->produtos()->orderByDesc('created_at')->get()]);
    }

    public function create()
    {
        $data = request()->all();
        $venda = $data['produtos_venda'] ?? 0;
        $v = Venda::findOrFail($venda);
        return view('vendas.produtos.create', ['venda' => $v]);
    }

    public function store(ProdutoVendaRequest $r)
    {
        $data = $r->all();
        $venda = auth()->user()->vendas()->findOrFail($data['venda_id']);

        $data["preco"] = $this->trataFloat($data["preco"]);
        $data["quantidade"] = $this->trataFloat($data["quantidade"]);
        $data["desconto"] = $this->trataFloat($data["desconto"]);
        $data["acrescimo"] = 0.00;#$this->trataFloat($data["acrescimo"]);
        $data["total"] = $this->trataFloat($data["total"]);
        $p = $venda->produtos()->create($data);
        $p->atualizaEstoqueCadastro();
        $venda->calculaTotal();
        return true;
    }

    public function destroy($produtos_venda)
    {
        $venda_id = request('venda_id');
        $venda = auth()->user()->vendas()->findOrFail($venda_id);
        $p = $venda->produtos()->findOrFail($produtos_venda);
        $p->deletar();
        $venda->calculaTotal();
        return true;
    }

}
