<?php

namespace App\Http\Controllers;

use App\User;
use App\Venda;
use PDF;
use Illuminate\Support\Facades\App;

class TesteController extends Controller
{

    public function teste()
    {
        $user = User::where('user_token', 'WfrGkJGAGhB6zHhv')->first();
        // dd($user);
        $vendas = $user->vendas()->where('total', '>', 0.00)->where('concluida', 'S')->whereNull('vendas.iderp')->get();
        $pedidos = [];
        foreach ($vendas as $v) {
            $produtos = [];
            foreach ($v->produtos()->whereNull('iderp')->get() as $p)
                $produtos[$p->id] = [
                    'iderpproduto' => $p->produto()->first()->iderp,
                    'nome' => $p->nome,
                    'preco' => $p->preco,
                    'quantidade' => $p->quantidade,
                    'desconto' => $p->desconto,
                    'acrescimo' => $p->acrescimo,
                    'total' => $p->total
                ];
            $pedidos[$v->id] = [
                'iderpempresa' => $v->empresa()->first()->iderp,
                'iderpcliente' => $v->cliente()->first()->iderp,
                'iderptipovenda' => $v->tipoVenda()->first()->iderp,
                'iderpvendedor' => $v->vendedor()->first()->iderp,
                'total' => $v->total,
                'desconto' => $v->desconto,
                'acrescimo' => $v->acrescimo,
                'dtcadastro' => $v->created_at,
                'observacoes' => $v->observacoes,
                'produtos' => $produtos
            ];
        }
        return response()->json($pedidos);
    }

}
