<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Empresa;
use App\Produto;
use App\Rota;
use App\TiposVenda;
use App\User;
use App\UsersRota;

class ApiController extends Controller
{
    private $user;
    private $empresasAux = [];
    private $rotasAux = [];
    private $usersAux = [];
    private $statusError = 500;

    public function empresas($token)
    {
        $this->setUser($token);
        $data = request()->all();

        if (!isset($data['empresas']) and !$data['empresas'])
            return response()->json(['mensagem' => 'Nenhuma empresa informada. ' . print_r($data, true)], $this->statusError);

        $empresas = json_decode($data['empresas']);

        if (!$empresas)
            return response()->json(['mensagem' => 'Nenhuma empresa informada. ' . print_r($data['empresas'], true)], $this->statusError);

        foreach ($empresas as $e) {
            if (!isset($e->razao) or !isset($e->fantasia) or !isset($e->cnpj) or !isset($e->iderp) or !$e->razao or !$e->fantasia or !$e->cnpj or !$e->iderp)
                return response()->json(['mensagem' => 'Empresa inconsistente: ' . print_r($e, true)], $this->statusError);

            Empresa::updateOrCreate([
                'iderp' => $e->iderp,
                'user_id' => $this->user->id
            ], [
                'razao' => $e->razao,
                'fantasia' => $e->fantasia,
                'cnpj' => $e->cnpj,
                'iderp' => $e->iderp,
                'user_id' => $this->user->id
            ]);
        }
        return response()->json(['mensagem' => 'Empresa cadastradas com sucesso']);
    }

    public function clientes($token)
    {
        $this->setUser($token);
        $data = request()->all();

        if (!isset($data['clientes']) and !$data['clientes'])
            return response()->json(['mensagem' => 'Nenhum cliente informado.' . print_r($data, true)], $this->statusError);

        $clientes = json_decode($data['clientes']);

        if (!$clientes)
            return response()->json(['mensagem' => 'Nenhum cliente informado.' . print_r($data['clientes'], true)], $this->statusError);

        foreach ($clientes as $c) {
            if (!isset($c->nome) or !isset($c->documento) or !isset($c->iderp) or !isset($c->idempresaerp) or !$c->nome or !$c->documento or !$c->iderp or !$c->idempresaerp)
                return response()->json(['mensagem' => 'Cliente inconsistente: ' . print_r($c, true)], $this->statusError);

            if (isset($this->empresasAux[$c->idempresaerp]))
                $empresa_id = $this->empresasAux[$c->idempresaerp];
            else {
                $empresa = $this->user->empresas()->where('iderp', $c->idempresaerp)->first();

                if (!$empresa)
                    return response()->json(['mensagem' => 'Empresa n??o encontrada: ' . implode(';', $c)], $this->statusError);

                $this->empresasAux[$c->idempresaerp] = $empresa_id = $empresa->id;
            }
            if (!isset($c->idrotaerp) || !$c->idrotaerp)
                $rota_id = null;
            else if (isset($this->rotasAux[$c->idrotaerp]))
                $rota_id = $this->rotasAux[$c->idrotaerp];
            else {
                if (!$empresa)
                    $rota = Rota::where('iderp', $c->idrotaerp)->where('empresa_id', $empresa_id)->first();
                else
                    $rota = $empresa->rotas()->where('iderp', $c->idrotaerp)->first();

                if (!$rota)
                    return response()->json(['mensagem' => 'Rota n??o encontrada: ' . implode(';', $c)], $this->statusError);

                $this->rotasAux[$c->idrotaerp] = $rota_id = $rota->id;
            }
            Cliente::updateOrCreate([
                'empresa_id' => $empresa_id,
                'iderp' => $c->iderp
            ], [
                'nome' => $c->nome,
                'documento' => $c->documento,
                'ativo' => $c->ativo ?? 'N',
                'situacao' => $c->situacao ?? null,
                'saldo_pendente' => $c->saldo_pendente ?? null,
                'rota_id' => $rota_id
            ]);
        }
        return response()->json(['mensagem' => 'Cliente cadastrados com sucesso']);
    }

    public function tiposVendas($token)
    {
        $this->setUser($token);
        $data = request()->all();

        if (!isset($data['tiposvendas']) and !$data['tiposvendas'])
            return response()->json(['mensagem' => 'Nenhum tipo de venda informado. ' . print_r($data, true)], $this->statusError);

        $tiposvendas = json_decode($data['tiposvendas']);

        if (!$tiposvendas)
            return response()->json(['mensagem' => 'Nenhum tipo de venda informado. ' . print_r($data['tiposvendas'], true)], $this->statusError);

        foreach ($tiposvendas as $t) {
            if (!isset($t->nome) or !isset($t->iderp) or !isset($t->idempresaerp) or !$t->nome or !$t->iderp or !$t->idempresaerp)
                return response()->json(['mensagem' => 'Tipo de venda inconsistente: ' . print_r($t, true)], $this->statusError);

            if (isset($this->empresasAux[$t->idempresaerp]))
                $empresa_id = $this->empresasAux[$t->idempresaerp];
            else {
                $empresa = $this->user->empresas()->where('iderp', $t->idempresaerp)->first();

                if (!$empresa)
                    return response()->json(['mensagem' => 'Empresa n??o encontrada: ' . implode(';', $t)], $this->statusError);

                $this->empresasAux[$t->idempresaerp] = $empresa->id;
                $empresa_id = $empresa->id;
            }

            TiposVenda::updateOrCreate([
                'empresa_id' => $empresa_id,
                'iderp' => $t->iderp
            ], [
                'nome' => $t->nome,
                'ativo' => $t->ativo ?? 'N'
            ]);
        }
        return response()->json(['mensagem' => 'Tipos de vendas cadastrados com sucesso']);
    }

    public function produtos($token)
    {
        $this->setUser($token);
        $data = request()->all();

        if (!isset($data['produtos']) and !$data['produtos'])
            return response()->json(['mensagem' => 'Nenhum produto informado. ' . print_r($data, true)], $this->statusError);

        $produtos = json_decode($data['produtos']);

        if (!$produtos)
            return response()->json(['mensagem' => 'Nenhum produto informado. ' . print_r($data['produtos'], true)], $this->statusError);

        foreach ($produtos as $p) {
            if (!isset($p->nome) or !isset($p->iderp) or !isset($p->idempresaerp) or !isset($p->preco) or !isset($p->estoque) or !$p->nome or !$p->iderp or !$p->idempresaerp)
                return response()->json(['mensagem' => 'Produto de venda inconsistente: ' . print_r($p, true)], $this->statusError);

            if (isset($this->empresasAux[$p->idempresaerp]))
                $empresa_id = $this->empresasAux[$p->idempresaerp];
            else {
                $empresa = $this->user->empresas()->where('iderp', $p->idempresaerp)->first();

                if (!$empresa)
                    return response()->json(['mensagem' => 'Empresa n??o encontrada: ' . implode(';', $p)], $this->statusError);

                $this->empresasAux[$p->idempresaerp] = $empresa_id = $empresa->id;
            }

            Produto::updateOrCreate([
                'empresa_id' => $empresa_id,
                'iderp' => $p->iderp
            ], [
                'nome' => $p->nome,
                'preco' => $p->preco,
                'estoque' => $p->estoque,
                'ativo' => $p->ativo ?? 'N',
                'ean' => $p->ean ?? null,
                'referencia' => $p->referencia ?? null,
            ]);
        }
        return response()->json(['mensagem' => 'Produto cadastrados com sucesso']);
    }

    private function setUser($token)
    {
        $user = User::where('user_token', $token)->first();
        if (!$user)
            throw new \Exception('Usu??rio n??o encontrado');
        $this->user = $user;
    }

    public function getPedidos($token)
    {
        $data = request()->all();
        $this->setUser($token);
        $pedidos = [];
        $vAux = $this->user->vendas()->where('total', '>', 0.00)->where('concluida', 'S')->whereNull('vendas.iderp');
        if (isset($data['iderp'])) {
            $e = $this->user->empresas()->where('iderp', $data['iderp'])->first();
            if (!$e)
                return response()->json(['mensagem' => 'Empresa n??o encontrada (iderp informado: ' . $data['iderp'] . ')'], $this->statusError);
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

    public function setPedidos($token)
    {
        $this->setUser($token);
        $data = request('pedidos');
        if (!$data)
            return response()->json(['mensagem' => "Nenhum pedido informado. " . print_r($data, true)], $this->statusError);
        $pedidos = json_decode($data, true);

        foreach ($pedidos as $p) {
            $venda_id = $p['venda_id'];
            $iderp = $p['iderp'];
            $pedido = $this->user->vendas()->find($venda_id);
            if (!$pedido)
                return response()->json(['mensagem' => "Pedido {$venda_id} n??o encontrado (IDERP: {$iderp})."], $this->statusError);
            $pedido->iderp = $iderp;
            $pedido->update();
        }
        $numPedidos = count($pedidos);
        return response()->json(['mensagem' => $numPedidos . ' pedido' . ($numPedidos > 1 ? 's' : '') . ' atualizado' . ($numPedidos > 1 ? 's' : '')]);
    }


    public function rotas($token)
    {
        $this->setUser($token);
        $data = request()->all();

        if (!isset($data['rotas']) or !$data['rotas'])
            return response()->json(['mensagem' => 'Nenhuma rota informada.' . print_r($data, true)], $this->statusError);

        $rotas = json_decode($data['rotas']);

        if (!$rotas)
            return response()->json(['mensagem' => 'Nenhuma rota informada.' . print_r($data['rotas'], true)], $this->statusError);

        foreach ($rotas as $r) {
            if (!isset($r->nome) or !isset($r->iderp) or !isset($r->idempresaerp) or !$r->nome or !$r->iderp or !$r->idempresaerp)
                return response()->json(['mensagem' => 'Rota inconsistente: ' . print_r($r, true)], $this->statusError);

            if (isset($this->empresasAux[$r->idempresaerp]))
                $empresa_id = $this->empresasAux[$r->idempresaerp];
            else {
                $empresa = $this->user->empresas()->where('iderp', $r->idempresaerp)->first();

                if (!$empresa)
                    return response()->json(['mensagem' => 'Empresa n??o encontrada: ' . implode(';', $r)], $this->statusError);

                $this->empresasAux[$r->idempresaerp] = $empresa_id = $empresa->id;
            }
            Rota::updateOrCreate([
                'empresa_id' => $empresa_id,
                'iderp' => $r->iderp
            ], [
                'nome' => $r->nome,
            ]);
        }
        return response()->json(['mensagem' => 'Rotas cadastradas com sucesso']);
    }

    public function rotasFuncionarios($token)
    {
        $this->setUser($token);
        $data = request()->all();

        if (!isset($data['rotas']) or !$data['rotas'])
            return response()->json(['mensagem' => 'Nenhuma rota de funcion??rio informada.' . print_r($data, true)], $this->statusError);

        $rotas = json_decode($data['rotas']);

        if (!$rotas)
            return response()->json(['mensagem' => 'Nenhuma rota de funcion??rio informada.' . print_r($data['rotas'], true)], $this->statusError);

        $users_id_deletes = [];
        $users_rotas_create = [];
        foreach ($rotas as $r) {
            if (!isset($r->idrotaerp) or !isset($r->idfuncionarioerp) or !isset($r->idempresaerp) or !$r->idrotaerp or !$r->idfuncionarioerp or !$r->idempresaerp)
                return response()->json(['mensagem' => 'Rota funcion??rio inconsistente: ' . print_r($r, true)], $this->statusError);

            if (isset($this->empresasAux[$r->idempresaerp]))
                $empresa_id = $this->empresasAux[$r->idempresaerp];
            else {
                $empresa = $this->user->empresas()->where('iderp', $r->idempresaerp)->first();

                if (!$empresa)
                    return response()->json(['mensagem' => 'Empresa n??o encontrada: ' . implode(';', $r)], $this->statusError);

                $this->empresasAux[$r->idempresaerp] = $empresa_id = $empresa->id;
            }

            if (isset($this->rotasAux[$r->idrotaerp]))
                $rota_id = $this->rotasAux[$r->idrotaerp];
            else {
                $rota = $empresa->rotas()->where('iderp', $r->idrotaerp)->first();

                if (!$rota)
                    return response()->json(['mensagem' => 'Rota n??o encontrada: ' . implode(';', $r)], $this->statusError);

                $this->rotasAux[$r->idrotaerp] = $rota_id = $rota->id;
            }

            if (isset($this->usersAux[$r->idfuncionarioerp]))
                $user_id = $this->usersAux[$r->idfuncionarioerp];
            else {
                $user = $this->user->users()->where('iderp', $r->idfuncionarioerp)->first();

                if (!$user)
                    continue;//return response()->json(['mensagem' => 'Funcion??rio n??o encontrado, associe o vendedor ao ERP: ' . implode(';', $r)], $this->statusError);

                $this->usersAux[$r->idfuncionarioerp] = $user_id = $user->id;
            }
            $users_id_deletes[] = $user_id;
            $users_rotas_create[] = [
                'user_id' => $user_id,
                'rota_id' => $rota_id
            ];
        }
        foreach ($users_id_deletes as $id) {
            UsersRota::where('user_id', $id)->delete();
        }
        foreach ($users_rotas_create as $data) {
            UsersRota::create($data);
        }
        return response()->json(['mensagem' => 'Rotas associadas com sucesso']);
    }

}
