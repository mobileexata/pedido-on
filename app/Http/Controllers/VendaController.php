<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendaRequest;
use App\Venda;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VendaController extends Controller
{

    public function index(Request $request)
    {
        $query_param_search = $request->get('q');
        $data_inicial = $request->get('data_inicial');
        $data_final = $request->get('data_final');
        if (empty($data_inicial)) {
            $data_inicial = now()->subMonth()->format('Y-m-d');
        }
        if (empty($data_final)) {
            $data_final = now()->format('Y-m-d');
        }
        $vendas = auth()
            ->user()
            ->vendas()
            ->orderByDesc('created_at')
            ->when(!empty($query_param_search), function ($query) use ($query_param_search) {
                return $query->whereExists(function ($query) use ($query_param_search) {
                    $query->select(DB::raw(1))
                        ->from('clientes')
                        ->whereColumn('vendas.cliente_id', 'clientes.id')
                        ->where('clientes.nome', 'like', "%$query_param_search%")
                        ->orWhere('clientes.documento', 'like', "%$query_param_search %");
                });
            })
            ->whereBetween('vendas.created_at', [
                $data_inicial . ' 00:00:00',
                $data_final . ' 23:59:59'
                ])
            ->when($request->get('pendente_importacao') == 'on', function ($query) {
                $query->whereNull('vendas.iderp');
            });
        return view('vendas.index', [
            'vendas' => $vendas->paginate(),
            'q' => $request->get('q'),
            'data_inicial' => $request->get('data_inicial'),
            'data_final' => $request->get('data_final'),
            'pendente_importacao' => $request->get('pendente_importacao'),
        ]);
    }

    public function create()
    {
        return view('vendas.create');
    }

    public function clientes($empresa)
    {
        $request = request()->all();
        $data['results'] = [];
        $empresa = auth()->user()->empresas()->findOrFail($empresa);
        $clientes = $empresa->clientes()->where('ativo', 'S')->take(50);
        if (auth()->user()->user_id and auth()->user()->rotas()->count())
            $clientes->whereIn('rota_id', function ($query) {
                return $query->from('users_rotas')
                    ->select('rota_id')
                    ->distinct()
                    ->where('user_id', auth()->id());
            });

        if (isset($request['q']) and $request['q'])
            $clientes->where('nome', 'like', "%{$request['q']}%")->orWhere('documento', 'like', "%{$request['q']}%");
        foreach ($clientes->get() as $c) {
            $saldo_pendente_formatado = $this->getSaldoPendenteFormatado($c->saldo_pendente);
            $situacao_saldo_calc = $this->getTextSaldoSituacao($c->situacao, $c->saldo_pendente, $saldo_pendente_formatado);
            $data['results'][] = [
                'id' => $c->id,
                'text' => $c->nome,
                'situacao' => $c->situacao ?? '-',
                'saldo_pendente' => $saldo_pendente_formatado,
                'situacao_saldo_calc' => $situacao_saldo_calc
            ];
        }
        return response()->json($data);
    }

    private function getSaldoPendenteFormatado($saldo_pendente)
    {
        return 'R$' . number_format($saldo_pendente, 2, ',', '.');
    }

    private function getTextSaldoSituacao($situacao, $saldo_pendente, $saldo_pendente_formatado = null)
    {
        if (!$saldo_pendente_formatado)
            $saldo_pendente_formatado = $this->getSaldoPendenteFormatado($saldo_pendente);
        $situacao_saldo_calc = ": (";
        if ($situacao)
            $situacao_saldo_calc .= "Sit: {$situacao}";
        $situacao_saldo_calc .= ($situacao ? " - " : null) . "Saldo Pend: $saldo_pendente_formatado";
        $situacao_saldo_calc .= ")";
        return $situacao_saldo_calc;
    }

    public function produtos($empresa)
    {
        $request = request()->all();
        $data['results'] = [];
        $empresa = auth()->user()->empresas()->findOrFail($empresa);
        $produtos = $empresa->produtos()->where('ativo', 'S')->take(50);
        if (isset($request['q']) and $request['q'])
            $produtos->where(function ($query) use ($request) {
                return $query->orWhere('referencia', 'like', "%{$request['q']}%")
                    ->orWhere('nome', 'like', "%{$request['q']}%")
                    ->orWhere('iderp', 'like', "%{$request['q']}%")
                    ->orWhere('ean', 'like', "%{$request['q']}%");
                });

        $prod = $produtos->get();
        if ($prod->count()) {
            $data['results'][] = [
                'id' => null,
                'nome' => '0.00',
                'preco' => '0.00',
                'preco_formatado' => 'R$0.00',
                'estoque' => '0.00',
                'iderp' => null,
            ];
            foreach ($produtos->get() as $p) {
                $data['results'][] = [
                    'id' => $p->id,
                    'nome' => $p->nome,
                    'referencia' => $p->referencia,
                    'imagem' => ($p->imagem) ? asset('produtos/' . $p->imagem) : asset('images/no_photo.png'),
                    'preco' => number_format($p->preco, 2),
                    'preco_formatado' => 'R$' . number_format($p->preco, 2, ',', '.'),
                    'estoque' => number_format($p->estoque, 2),
                    'estoque_formatado' => number_format($p->estoque, 2, ',', '.'),
                    'iderp' => $p->iderp,
                ];
            }
        }
        return response()->json($data);
    }

    public function tiposVendas($empresa)
    {
        $request = request()->all();
        $data['results'] = [];
        $empresa = auth()->user()->empresas()->findOrFail($empresa);
        $tiposVendas = $empresa->tiposVendas()->where('ativo', 'S')->take(50);
        if (isset($request['q']) and $request['q'])
            $tiposVendas->where('nome', 'like', "%{$request['q']}%");
        foreach ($tiposVendas->get() as $p) {
            $data['results'][] = [
                'id' => $p->id,
                'text' => $p->nome
            ];
        }
        return response()->json($data);
    }

    public function store(VendaRequest $r)
    {
        $data = $r->all();
        $data['user_id'] = auth()->id();
        $data['owner_user_id'] = auth()->user()->user_id ?? auth()->id();
        $venda = auth()->user()->vendas()->create($data);
        return redirect()->route('vendas.edit', ['venda' => $venda->id]);
    }

    public function edit($venda)
    {
        $v = auth()->user()->vendas()->findOrFail($venda);
        if ($v->iderp)
            return back()->with('status_error', 'Venda já importada no ERP, não será altera-lá');
        $cliente = $v->cliente()->first();
        $situacao_saldo_calc = $this->getTextSaldoSituacao($cliente->situacao, $cliente->saldo_pendente);
        return view('vendas.edit', ['venda' => $v, 'situacao_saldo_calc' => $situacao_saldo_calc]);
    }

    public function update(VendaRequest $r, $venda)
    {
        $data = $r->all();
        $v = auth()->user()->vendas()->findOrFail($venda);
        if (isset($data['desconto']))
            $data['desconto'] = $this->trataFloat($data['desconto']);
        if (isset($data['acrescimo']))
            $data['acrescimo'] = $this->trataFloat($data['acrescimo']);
        if (isset($data['total']))
            $data['total'] = $this->trataFloat($data['total']);
        $v->update($data);
        $v->calculaTotal();
        return redirect()->route('vendas.index');
    }

    public function destroy($venda)
    {
        $v = Venda::findOrFail($venda);
        $v->deletar();
        return redirect()->route('vendas.index');
    }

    public function totalizacaoVenda(Venda $venda)
    {
        return view('vendas.totalizacao', ['venda' => $venda]);
    }

    public function finalizacaoVenda(Request $request, Venda $venda)
    {
        if ($request->get('cliente_id')) {
            $venda->cliente_id = $request->input('cliente_id');
        }

        if ($request->get('tiposvenda_id')) {
            $venda->tiposvenda_id = $request->input('tiposvenda_id');
        }

        if ($request->get('cliente_id') || $request->get('tiposvenda_id')) {
            $venda->save();
        }

        return view('vendas.finalizacao', ['venda' => $venda]);
    }

    public function show(Venda $venda)
    {
        $data = request()->all();
        if (isset($data['pdf'])) {
            $view = view('vendas.showpdf', ['venda' => $venda])->render();
            $pdf = PDF::loadHtml($view);
            return $pdf->stream('pedido-' . ($venda->iderp ?? $venda->id) . '-' . $venda->empresa()->first()->razao . '.pdf');
        }
        return view('vendas.show', ['venda' => $venda]);
    }
}
