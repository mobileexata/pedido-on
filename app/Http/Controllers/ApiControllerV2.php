<?php

namespace App\Http\Controllers;

use Exception;
use App\Cliente;
use App\Empresa;
use App\Http\Requests\ClientesRequest;
use App\Http\Requests\EmpresasRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiControllerV2 extends Controller
{
    public function empresas(Request $request)
    {
        $request->validate([
            '*.iderp' => ['required', 'integer'],
            '*.razao' => ['required', 'string', 'max:191'],
            '*.fantasia' => ['required', 'string', 'max:191'],
            '*.cnpj' => ['required', 'string', 'max:191'],
        ]);

        try {
            foreach ($request->all() as $e) {
                Empresa::updateOrCreate([
                    'iderp' => $e->iderp,
                    'user_id' => $request->user()->id
                ], [
                    'razao' => $e->razao,
                    'fantasia' => $e->fantasia,
                    'cnpj' => $e->cnpj,
                    'iderp' => $e->iderp,
                    'user_id' => $request->user()->id
                ]);
            }
            return response()->json(null, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->returnResponseError($e, 'error creating empresas');
        }
    }

    public function clientes(ClientesRequest $request)
    {
        try {
            foreach ($request->input('clientes') as $c) {
                $empresa = $request->user()->empresas()->where('iderp', $c->idempresaerp)->first();
                if (!$empresa) {
                    return response()->json(['error' => 'cliente not found', 'data' => $c], 400);
                }
                
                $rota = $empresa->rotas()->where('iderp', $c->idrotaerp)->first();
                if (!$rota) {
                    return response()->json(['error' => 'rota not found', 'data' => $c], 400);
                }
                    
                
                Cliente::updateOrCreate([
                    'empresa_id' => $$empresa->id,
                    'iderp' => $c->iderp
                ], [
                    'nome' => $c->nome,
                    'documento' => $c->documento,
                    'ativo' => $c->ativo,
                    'situacao' => $c->situacao ?? null,
                    'saldo_pendente' => $c->saldo_pendente ?? null,
                    'rota_id' => $rota->id
                ]);
            }
            return response()->json(['mensagem' => 'Cliente cadastrados com sucesso']);
        } catch (Exception $e) {
            $this->returnResponseError($e, 'error creating clientes');
        }
    }

    // public function tiposVendas($token)
    // {
    //     $this->setUser($token);
    //     $data = request()->all();

    //     if (!isset($data['tiposvendas']) and !$data['tiposvendas'])
    //         return response()->json(['mensagem' => 'Nenhum tipo de venda informado. ' . print_r($data, true)], $this->statusError);

    //     $tiposvendas = json_decode($data['tiposvendas']);

    //     if (!$tiposvendas)
    //         return response()->json(['mensagem' => 'Nenhum tipo de venda informado. ' . print_r($data['tiposvendas'], true)], $this->statusError);

    //     foreach ($tiposvendas as $t) {
    //         if (!isset($t->nome) or !isset($t->iderp) or !isset($t->idempresaerp) or !$t->nome or !$t->iderp or !$t->idempresaerp)
    //             return response()->json(['mensagem' => 'Tipo de venda inconsistente: ' . print_r($t, true)], $this->statusError);

    //         if (isset($this->empresasAux[$t->idempresaerp]))
    //             $empresa_id = $this->empresasAux[$t->idempresaerp];
    //         else {
    //             $empresa = $request->user()->empresas()->where('iderp', $t->idempresaerp)->first();

    //             if (!$empresa)
    //                 return response()->json(['mensagem' => 'Empresa não encontrada: ' . implode(';', $t)], $this->statusError);

    //             $this->empresasAux[$t->idempresaerp] = $empresa->id;
    //             $empresa_id = $empresa->id;
    //         }

    //         TiposVenda::updateOrCreate([
    //             'empresa_id' => $empresa_id,
    //             'iderp' => $t->iderp
    //         ], [
    //             'nome' => $t->nome,
    //             'ativo' => $t->ativo ?? 'N'
    //         ]);
    //     }
    //     return response()->json(['mensagem' => 'Tipos de vendas cadastrados com sucesso']);
    // }

    // public function produtos($token)
    // {
    //     $this->setUser($token);
    //     $data = request()->all();

    //     if (!isset($data['produtos']) and !$data['produtos'])
    //         return response()->json(['mensagem' => 'Nenhum produto informado. ' . print_r($data, true)], $this->statusError);

    //     $produtos = json_decode($data['produtos']);

    //     if (!$produtos)
    //         return response()->json(['mensagem' => 'Nenhum produto informado. ' . print_r($data['produtos'], true)], $this->statusError);

    //     foreach ($produtos as $p) {
    //         if (!isset($p->nome) or !isset($p->iderp) or !isset($p->idempresaerp) or !isset($p->preco) or !isset($p->estoque) or !$p->nome or !$p->iderp or !$p->idempresaerp)
    //             return response()->json(['mensagem' => 'Produto de venda inconsistente: ' . print_r($p, true)], $this->statusError);

    //         $empresa = $request->user()->empresas()->where('iderp', $p->idempresaerp)->first();

    //         if (!$empresa)
    //             return response()->json(['mensagem' => 'Empresa não encontrada: ' . implode(';', $p)], $this->statusError);

    //         $fabricante_id = null;
    //         if ($p->fabricante_id) {
    //             $fabricante = $empresa->fabricantes->where('iderp', $p->fabricante_id)->first();
                
    //             if (!$fabricante) {
    //                 return response()->json(['mensagem' => "fabricante não encontrado iderp recebido: {$p->fabricante_id}"], $this->statusError);
    //             }
                
    //             $fabricante_id = $fabricante->id;
    //         }
            

    //         Produto::updateOrCreate([
    //             'empresa_id' => $empresa->id,
    //             'iderp' => $p->iderp
    //         ], [
    //             'nome' => $p->nome,
    //             'preco' => $p->preco,
    //             'estoque' => $p->estoque,
    //             'ativo' => $p->ativo ?? 'N',
    //             'ean' => $p->ean ?? null,
    //             'referencia' => $p->referencia ?? null,
    //             'fabricante_id' => $fabricante_id,
    //         ]);
    //     }
    //     return response()->json(['mensagem' => 'Produto cadastrados com sucesso']);
    // }
    
    // public function getPedidos($token)
    // {
    //     $data = request()->all();
    //     $this->setUser($token);
    //     $pedidos = [];
    //     $vAux = $request->user()->vendas()->where('total', '>', 0.00)->where('concluida', 'S')->whereNull('vendas.iderp');
    //     if (isset($data['iderp'])) {
    //         $e = $request->user()->empresas()->where('iderp', $data['iderp'])->first();
    //         if (!$e)
    //             return response()->json(['mensagem' => 'Empresa não encontrada (iderp informado: ' . $data['iderp'] . ')'], $this->statusError);
    //         $vAux->where('empresa_id', $e->id);
    //     }
    //     $vendas = $vAux->get();
    //     foreach ($vendas as $v) {
    //         $produtos = [];
    //         foreach ($v->produtos()->whereNull('iderp')->get() as $p)
    //             $produtos[$p->id] = [
    //                 'iderpproduto' => $p->produto()->first()->iderp,
    //                 'nome' => $p->nome,
    //                 'preco' => $p->preco,
    //                 'quantidade' => $p->quantidade,
    //                 'desconto' => $p->desconto,
    //                 'acrescimo' => $p->acrescimo,
    //                 'total' => $p->total
    //             ];
    //         $pedidos[$v->id] = [
    //             'iderpempresa' => $v->empresa()->first()->iderp,
    //             'iderpcliente' => $v->cliente()->first()->iderp,
    //             'iderptipovenda' => $v->tipoVenda()->first()->iderp,
    //             'iderpvendedor' => $v->vendedor()->first()->iderp,
    //             'total' => $v->total,
    //             'desconto' => $v->desconto,
    //             'acrescimo' => $v->acrescimo,
    //             'dtcadastro' => $v->created_at,
    //             'observacoes' => $v->observacoes,
    //             'produtos' => $produtos
    //         ];
    //     }
    //     return response()->json($pedidos);
    // }

    // public function setPedidos($token)
    // {
    //     $this->setUser($token);
    //     $data = request('pedidos');
    //     if (!$data)
    //         return response()->json(['mensagem' => "Nenhum pedido informado. " . print_r($data, true)], $this->statusError);
    //     $pedidos = json_decode($data, true);

    //     foreach ($pedidos as $p) {
    //         $venda_id = $p['venda_id'];
    //         $iderp = $p['iderp'];
    //         $pedido = $request->user()->vendas()->find($venda_id);
    //         if (!$pedido)
    //             return response()->json(['mensagem' => "Pedido {$venda_id} não encontrado (IDERP: {$iderp})."], $this->statusError);
    //         $pedido->iderp = $iderp;
    //         $pedido->update();
    //     }
    //     $numPedidos = count($pedidos);
    //     return response()->json(['mensagem' => $numPedidos . ' pedido' . ($numPedidos > 1 ? 's' : '') . ' atualizado' . ($numPedidos > 1 ? 's' : '')]);
    // }


    // public function rotas($token)
    // {
    //     $this->setUser($token);
    //     $data = request()->all();

    //     if (!isset($data['rotas']) or !$data['rotas'])
    //         return response()->json(['mensagem' => 'Nenhuma rota informada.' . print_r($data, true)], $this->statusError);

    //     $rotas = json_decode($data['rotas']);

    //     if (!$rotas)
    //         return response()->json(['mensagem' => 'Nenhuma rota informada.' . print_r($data['rotas'], true)], $this->statusError);

    //     foreach ($rotas as $r) {
    //         if (!isset($r->nome) or !isset($r->iderp) or !isset($r->idempresaerp) or !$r->nome or !$r->iderp or !$r->idempresaerp)
    //             return response()->json(['mensagem' => 'Rota inconsistente: ' . print_r($r, true)], $this->statusError);

    //         if (isset($this->empresasAux[$r->idempresaerp]))
    //             $empresa_id = $this->empresasAux[$r->idempresaerp];
    //         else {
    //             $empresa = $request->user()->empresas()->where('iderp', $r->idempresaerp)->first();

    //             if (!$empresa)
    //                 return response()->json(['mensagem' => 'Empresa não encontrada: ' . implode(';', $r)], $this->statusError);

    //             $this->empresasAux[$r->idempresaerp] = $empresa_id = $empresa->id;
    //         }
    //         Rota::updateOrCreate([
    //             'empresa_id' => $empresa_id,
    //             'iderp' => $r->iderp
    //         ], [
    //             'nome' => $r->nome,
    //         ]);
    //     }
    //     return response()->json(['mensagem' => 'Rotas cadastradas com sucesso']);
    // }

    // public function rotasFuncionarios($token)
    // {
    //     $this->setUser($token);
    //     $data = request()->all();

    //     if (!isset($data['rotas']) or !$data['rotas'])
    //         return response()->json(['mensagem' => 'Nenhuma rota de funcionário informada.' . print_r($data, true)], $this->statusError);

    //     $rotas = json_decode($data['rotas']);

    //     if (!$rotas)
    //         return response()->json(['mensagem' => 'Nenhuma rota de funcionário informada.' . print_r($data['rotas'], true)], $this->statusError);

    //     $users_id_deletes = [];
    //     $users_rotas_create = [];
    //     foreach ($rotas as $r) {
    //         if (!isset($r->idrotaerp) or !isset($r->idfuncionarioerp) or !isset($r->idempresaerp) or !$r->idrotaerp or !$r->idfuncionarioerp or !$r->idempresaerp)
    //             return response()->json(['mensagem' => 'Rota funcionário inconsistente: ' . print_r($r, true)], $this->statusError);

    //         if (isset($this->empresasAux[$r->idempresaerp]))
    //             $empresa_id = $this->empresasAux[$r->idempresaerp];
    //         else {
    //             $empresa = $request->user()->empresas()->where('iderp', $r->idempresaerp)->first();

    //             if (!$empresa)
    //                 return response()->json(['mensagem' => 'Empresa não encontrada: ' . implode(';', $r)], $this->statusError);

    //             $this->empresasAux[$r->idempresaerp] = $empresa_id = $empresa->id;
    //         }

    //         if (isset($this->rotasAux[$r->idrotaerp]))
    //             $rota_id = $this->rotasAux[$r->idrotaerp];
    //         else {
    //             $rota = $empresa->rotas()->where('iderp', $r->idrotaerp)->first();

    //             if (!$rota)
    //                 return response()->json(['mensagem' => 'Rota não encontrada: ' . implode(';', $r)], $this->statusError);

    //             $this->rotasAux[$r->idrotaerp] = $rota_id = $rota->id;
    //         }

    //         if (isset($request->user()sAux[$r->idfuncionarioerp]))
    //             $user_id = $request->user()sAux[$r->idfuncionarioerp];
    //         else {
    //             $user = $request->user()->users()->where('iderp', $r->idfuncionarioerp)->first();

    //             if (!$user)
    //                 continue; //return response()->json(['mensagem' => 'Funcionário não encontrado, associe o vendedor ao ERP: ' . implode(';', $r)], $this->statusError);

    //             $request->user()sAux[$r->idfuncionarioerp] = $user_id = $user->id;
    //         }
    //         $users_id_deletes[] = $user_id;
    //         $users_rotas_create[] = [
    //             'user_id' => $user_id,
    //             'rota_id' => $rota_id
    //         ];
    //     }
    //     foreach ($users_id_deletes as $id) {
    //         UsersRota::where('user_id', $id)->delete();
    //     }
    //     foreach ($users_rotas_create as $data) {
    //         UsersRota::create($data);
    //     }
    //     return response()->json(['mensagem' => 'Rotas associadas com sucesso']);
    // }

    // public function fabricantes(Request $request, $token)
    // {
    //     $this->setUser($token);

    //     if (!request()->input('fabricantes'))
    //         return response()->json(['mensagem' => 'Nenhum fabricante informado.'], $this->statusError);

    //     $fabricantes = json_decode(request()->input('fabricantes'));

    //     if (!$fabricantes)
    //         return response()->json(['mensagem' => 'Nenhum fabricante informado'], $this->statusError);

    //     foreach ($fabricantes as $f) {
    //         if (!isset($f->nome) or !isset($f->iderp) or !isset($f->idempresaerp) or !$f->nome or !$f->iderp or !$f->idempresaerp)
    //             return response()->json(['mensagem' => 'Fabricante inconsistente: ' . print_r($f, true)], $this->statusError);

    //         if (isset($this->empresasAux[$f->idempresaerp]))
    //             $empresa_id = $this->empresasAux[$f->idempresaerp];
    //         else {
    //             $empresa = $request->user()->empresas()->where('iderp', $f->idempresaerp)->first();

    //             if (!$empresa)
    //                 return response()->json(['mensagem' => 'Empresa não encontrada: ' . implode(';', $f)], $this->statusError);

    //             $this->empresasAux[$f->idempresaerp] = $empresa_id = $empresa->id;
    //         }
    //         Fabricante::updateOrCreate([
    //             'empresa_id' => $empresa_id,
    //             'iderp' => $f->iderp
    //         ], [
    //             'nome' => $f->nome,
    //         ]);
    //     }
    //     return response()->json(['mensagem' => 'Fabricantes cadastrados com sucesso']);
    // }
}
