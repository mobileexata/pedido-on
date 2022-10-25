<?php

namespace App\Http\Controllers;

use Exception;
use App\Cliente;
use App\Http\Requests\Api\ClientesRequest;
use App\Http\Requests\Api\EmpresasRequest;
use App\Http\Requests\Api\ProdutosRequest;
use App\Http\Requests\Api\TiposVendasRequest;
use App\Http\Resources\PedidoCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiControllerV2 extends Controller
{
    public function empresas(EmpresasRequest $request)
    {
        try {
            foreach ($request->all() as $empresa) {
                $request->user()->empresas()->updateOrCreate([
                    'iderp' => $empresa['iderp'],
                    'user_id' => $request->user()->id
                ], [
                    'razao' => $empresa['razao'],
                    'fantasia' => $empresa['fantasia'],
                    'cnpj' => $empresa['cnpj'],
                    'iderp' => $empresa['iderp'],
                    'user_id' => $request->user()->id
                ]);
            }
            return response()->json();
        } catch (Exception $e) {
            $this->returnResponseError($e, 'error creating empresas');
        }
    }

    public function clientes(ClientesRequest $request)
    {
        try {
            foreach ($request->all() as $cliente) {
                $empresa = $request->user()->empresas()->where('iderp', $cliente->idempresaerp)->first();
                if (!$empresa) {
                    return response()->json(['error' => 'empresa não encontrada', 'data' => $cliente], 400);
                }
                
                $rota = $empresa->rotas()->where('iderp', $cliente->idrotaerp)->first();
                if (!$rota) {
                    return response()->json(['error' => 'rota não encontrada', 'data' => $cliente], 400);
                }
                    
                $empresa->clientes()->updateOrCreate([
                    'empresa_id' => $$empresa->id,
                    'iderp' => $cliente->iderp
                ], [
                    'nome' => $cliente->nome,
                    'documento' => $cliente->documento,
                    'ativo' => $cliente->ativo,
                    'situacao' => $cliente->situacao ?? null,
                    'saldo_pendente' => $cliente->saldo_pendente ?? null,
                    'rota_id' => $rota->id
                ]);
            }
            return response()->json();
        } catch (Exception $e) {
            $this->returnResponseError($e, 'error creating clientes');
        }
    }

    public function tiposVendas(TiposVendasRequest $request)
    {
        foreach ($request->all() as $tipo_venda) {
            $empresa = $request->user()->empresas()->where('iderp', $tipo_venda->idempresaerp)->first();

            if (!$empresa)
                return response()->json(['error' => 'empresa não encontrada', 'data' => $tipo_venda ], 400);

                $empresa->tiposVendas()->updateOrCreate([
                'empresa_id' => $empresa->id,
                'iderp' => $tipo_venda->iderp
            ], [
                'nome' => $tipo_venda->nome,
                'ativo' => $tipo_venda->ativo
            ]);
        }
        return response()->json();
    }

    public function produtos(ProdutosRequest $request)
    {
        foreach ($request->all() as $produto) {
            $empresa = $request->user()->empresas()->where('iderp', $produto->idempresaerp)->first();
            if (!$empresa)
                return response()->json(['mensagem' => 'empresa não encontrada', 'data' => $produto], 400);

            $fabricante = $empresa->fabricantes()->where('iderp', $produto->idfabricanteerp)->first();
            if (!$fabricante) {
                return response()->json(['mensagem' => "fabricante não encontrado iderp recebido: {$produto->idfabricanteerp}"], 400);
            }
            
            $empresa->produtos()->updateOrCreate([
                'empresa_id' => $empresa->id,
                'iderp' => $produto->iderp
            ], [
                'nome' => $produto->nome,
                'preco' => $produto->preco,
                'estoque' => $produto->estoque,
                'ativo' => $produto->ativo,
                'ean' => $produto->ean,
                'referencia' => $produto->referencia,
                'fabricante_id' => $fabricante->id,
            ]);
        }
        return response()->json();
    }
    
    public function getPedidos(Request $request)
    {
        $pedido = [];
        $vAux = $request
            ->user()
            ->vendas()
            ->where('total', '>', 0.00)
            ->where('concluida', 'S')
            ->whereNull('vendas.iderp');
        if ($request->has('iderp')) {
            $e = $request->user()->empresas()->where('iderp', $request->get('iderp'))->first();
            if (!$e)
                return response()->json(['error' => 'Empresa não encontrada (iderp informado: ' . $request->get('iderp') . ')'], 400);
            $vAux->where('empresa_id', $e->id);
        }
        $vAux->dd();
        $vendas = $vAux->get();
        
        return response()->json(PedidoCollection::collection($vendas));
    }

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
