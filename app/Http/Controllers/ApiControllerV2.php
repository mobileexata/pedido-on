<?php

namespace App\Http\Controllers;

use Exception;
use App\Cliente;
use App\Fabricante;
use App\Http\Requests\ClientesRequest;
use App\Http\Requests\EmpresasRequest;
use App\Http\Requests\FabricantesRequest;
use App\Http\Requests\ProdutosRequest;
use App\Http\Requests\RotasFuncionariosRequest;
use App\Http\Requests\RotasRequest;
use App\Http\Requests\SetPedidosRequest;
use App\Http\Requests\TiposVendaRequest;
use App\Produto;
use App\Rota;
use App\TiposVenda;
use App\UsersRota;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiControllerV2 extends Controller
{
    public function empresas(EmpresasRequest $request)
    {
        try {
            $data = $request->all();
            foreach ($data as $e) {
                $request->user()->empresas()->updateOrCreate([
                    'iderp' => $e['iderp'],
                    'user_id' => $request->user()->id
                ], [
                    'razao' => $e['razao'],
                    'fantasia' => $e['fantasia'],
                    'cnpj' => $e['cnpj'],
                    'iderp' => $e['iderp'],
                    'user_id' => $request->user()->id
                ]);
            }
            return response()->json(['mensagem' => 'Empresas cadastradas com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error creating empresas');
        }
    }

    public function clientes(ClientesRequest $request)
    {
        try {
            foreach ($request->all() as $c) {
                $empresa = $request->user()->empresas()->where('iderp', $c['idempresaerp'])->first();
                if (!$empresa) {
                    return response()->json(['error' => 'empresa informada não encontrada', 'data' => $c], 400);
                }

                $rota = $empresa->rotas()->where('iderp', $c['idrotaerp'])->first();
                if (!$rota) {
                    return response()->json(['error' => 'rota não encontrada', 'data' => $c], 400);
                }

                Cliente::updateOrCreate([
                    'empresa_id' => $empresa->id,
                    'iderp' => $c['iderp']
                ], [
                    'nome' => $c['nome'],
                    'documento' => $c['documento'],
                    'ativo' => $c['ativo'],
                    'situacao' => $c['situacao'] ?? null,
                    'saldo_pendente' => $c['saldo_pendente'] ?? null,
                    'rota_id' => $rota->id
                ]);
            }
            return response()->json(['mensagem' => 'Cliente cadastrados com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error creating clientes');
        }
    }

    public function fabricantes(FabricantesRequest $request)
    {
        try {
            foreach ($request->all() as $f) {
                $empresa = $request->user()->empresas()->where('iderp', $f['idempresaerp'])->first();
                if (!$empresa) {
                    return response()->json(['error' => 'empresa informada não encontrada', 'data' => $f], 400);
                }

                Fabricante::updateOrCreate([
                    'empresa_id' => $empresa->id,
                    'iderp' => $f['iderp']
                ], [
                    'nome' => $f['nome'],
                ]);
            }
            return response()->json(['mensagem' => 'Fabricantes cadastrados com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error creating fabricantes');
        }
    }

    public function tiposVendas(TiposVendaRequest $request)
    {
        try {
            foreach ($request->all() as $t) {
                $empresa = $request->user()->empresas()->where('iderp', $t['idempresaerp'])->first();
                if (!$empresa) {
                    return response()->json(['error' => 'empresa informada não encontrada', 'data' => $t], 400);
                }
                TiposVenda::updateOrCreate([
                    'empresa_id' => $empresa->id,
                    'iderp' => $t['iderp']
                ], [
                    'nome' => $t['nome'],
                    'ativo' => $t['ativo'] ?? 'N',
                    'idtipoprecoerp' => $t['idtipoprecoerp'] ?? 1,
                    'desctipopreco' => $t['desctipopreco'] ?? 1
                ]);
            }
            return response()->json(['mensagem' => 'Tipos de vendas cadastrados com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error creating tipos de vendas');
        }
    }

    public function produtos(ProdutosRequest $request)
    {
        $data = $request->all();
        if (count($data) == 0) {
            return response()->json(['mensagem' => 'nenhum produto informado', 'body' => $data], 400);
        }

        try {
            foreach ($data as $p) {
                $empresa = $request->user()->empresas()->where('iderp', $p['idempresaerp'])->first();
                if (!$empresa) {
                    return response()->json(['error' => 'empresa informada não encontrada', 'produto_inconsistente' => $p], 400);
                }

                $fabricante_id = null;
                if ($p['fabricante_id']) {
                    $fabricante = $empresa->fabricantes->where('iderp', $p['fabricante_id'])->first();

                    if (!$fabricante) {
                        return response()->json(['error' => 'fabricante não encontrado', 'produto' => $p], 400);
                    }

                    $fabricante_id = $fabricante->id;
                }

                $precos = isset($p['precos']) ? collect($p['precos'])->pluck("vlpreco", "codtipopreco")->toArray() : ["1" => isset($p['preco']) ? $p['preco'] : 0];
                $custos = isset($p['custos']) ? collect($p['custos'])->pluck("vlcusto", "codempresa")->toArray() : ["1" => 0];
                $grupos = isset($p['grupo']) ? collect($p['grupo'])->toArray() : ["codgrupo" => 1, "descgrupo" => "GERAL", "codsubgrupo" => 1, "descsubgrupo" => "GERAL"];

                Produto::updateOrCreate([
                    'empresa_id' => $empresa->id,
                    'iderp' => $p['iderp']
                ], [
                    'nome' => $p['nome'],
                    'preco' => $p['preco'] ?? 0,
                    'precos' => $precos,
                    'custos' => $custos,
                    'estoque' => $p['estoque'],
                    'ativo' => $p['ativo'] ?? 'N',
                    'ean' => $p['ean'] ?? null,
                    'referencia' => $p['referencia'] ?? null,
                    'fabricante_id' => $fabricante_id,
                    'grupo' => $grupos,
                ]);
            }
            return response()->json(['mensagem' => 'Produto cadastrados com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error creating produtos');
        }
    }

    public function getPedidos(Request $request)
    {
        try {
            $data = $request->all();
            $pedidos = [];
            $vAux = $request->user()->vendas()->where('total', '>', 0.00)->where('concluida', 'S')->whereNull('vendas.iderp');
            if (isset($data['iderp'])) {
                $e = $request->user()->empresas()->where('iderp', $data['iderp'])->first();
                if (!$e)
                    return response()->json(['mensagem' => 'empresa não encontrada', 'data' => $data], 400);
                $vAux->where('empresa_id', $e->id);
            }
            $vendas = $vAux->get();
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
                $pedidos[] = [
                    'idpedido' => $v->id,
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
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error getting pedidos');
        }
    }

    public function setPedidos(SetPedidosRequest $request)
    {
        $data = $request->all();
        if (count($data) == 0) {
            return response()->json(['mensagem' => 'nenhum pedido informado', 'body' => $data], 400);
        }

        try {
            foreach ($data as $p) {
                $venda_id = $p['venda_id'];
                $iderp = $p['iderp'];
                $pedido = $request->user()->vendas()->find($venda_id);
                if (!$pedido)
                    return response()->json(['mensagem' => "Pedido {$venda_id} não encontrado (IDERP: {$iderp})."], 400);
                $pedido->iderp = $iderp;
                $pedido->update();
            }
            return response()->json(['mensagem' => 'pedidos atualzados com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error creating clientes');
        }
    }


    public function rotas(RotasRequest $request)
    {
        try {
            foreach ($request->all() as $r) {
                $empresa = $request->user()->empresas()->where('iderp', $r['idempresaerp'])->first();
                if (!$empresa) {
                    return response()->json(['mensagem' => 'Empresa não encontrada: ' . implode(';', $r)], 400);
                }

                Rota::updateOrCreate([
                    'empresa_id' => $empresa->id,
                    'iderp' => $r['iderp']
                ], [
                    'nome' => $r['nome'],
                ]);
            }
            return response()->json(['mensagem' => 'Rotas cadastradas com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error setting pedidos');
        }
    }

    public function rotasFuncionarios(RotasFuncionariosRequest $request)
    {
        try {
            $users_id_deletes = [];
            $users_rotas_create = [];
            foreach ($request->all() as $r) {
                $empresa = $request->user()->empresas()->where('iderp', $r['idempresaerp'])->first();
                if (!$empresa) {
                    return response()->json(['mensagem' => 'empresa não encontrada: ', 'data' => $r], 400);
                }

                $rota = $empresa->rotas()->where('iderp', $r['idrotaerp'])->first();

                if (!$rota) {
                    return response()->json(['mensagem' => 'rota não encontrada: ', 'data' => $r], 400);
                }

                $user = $request->user()->users()->where('iderp', $r['idfuncionarioerp'])->first();
                if (!$user) {
                    continue;
                }
                $users_id_deletes[] = $user->id;
                $users_rotas_create[] = [
                    'user_id' => $user->id,
                    'rota_id' => $rota->id
                ];
            }
            foreach ($users_id_deletes as $id) {
                UsersRota::where('user_id', $id)->delete();
            }
            foreach ($users_rotas_create as $data) {
                UsersRota::create($data);
            }
            return response()->json(['mensagem' => 'Rotas associadas com sucesso']);
        } catch (Exception $e) {
            return $this->returnResponseError($e, 'error getting rotas funcionarios');
        }
    }

    public function clientesPendentes(Request $request)
    {
        $res = collect();

        $request->user()->empresas->each(function ($empresa) use ($res) {
            $empresa->clientes()->whereNull('iderp')->get()->each(function ($cliente) use ($empresa, $res) {
                $res->push([
                    "id" => $cliente->id,
                    "iderpempresa" => (int) $empresa->iderp,
                    "iderprota" => Rota::find($cliente->rota_id)->iderp,
                    "nome" => $cliente->nome,
                    "documento" => $cliente->documento,
                    "created_at" => date('Y-m-d', strtotime($cliente->created_at)),
                    "ativo" => $cliente->ativo,
                    "fantasia" => $cliente->fantasia,
                    "dt_nascimento" => date('Y-m-d', strtotime($cliente->dt_nascimento)),
                    "tp_pessoa" => $cliente->tp_pessoa,
                    "inscricao" => $cliente->inscricao,
                    "cep" => $cliente->cep,
                    "numero" => $cliente->numero,
                    "logradouro" => $cliente->logradouro,
                    "bairro" => $cliente->bairro,
                    "cidade" => $cliente->cidade,
                    "uf" => $cliente->uf,
                    "ponto_referencia" => $cliente->ponto_referencia,
                    "email" => $cliente->email,
                    "isento" => $cliente->isento,
                    "telefone" => $cliente->telefone,
                ]);
            });
        });

        return response()->json($res->toArray());
    }

    public function setClientesPendentes(Request $request)
    {
        $this->validate($request, [
            '*.idcliente' => 'required|exists:clientes,id',
            '*.idclienteerp' => 'required|integer'
        ]);
        $data = collect($request->all());

        $data->each(function ($association) {
            Cliente::whereNull('iderp')->where('id', $association['idcliente'])->update(['iderp' => $association['idclienteerp']]);
        });

        return response()->json(['mensagem' => 'clientes informados']);
    }
}
