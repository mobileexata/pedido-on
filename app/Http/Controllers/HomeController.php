<?php

namespace App\Http\Controllers;

use App\User;
use App\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = request()->all();
        if (!isset($data['data_inicial']))
            $data['data_inicial'] = date('Y-m-') . '01';
        if (!isset($data['data_final']))
            $data['data_final'] = date('Y-m-t');

        $qtdVendas = 0;
        $totalVendas = 0.00;
        $totalDescontos = 0.00;
        $totalAcrescimos = 0.00;
        $metas = [];
        $vendas = [];
        $nomes = [];

        if (auth()->user()->user_id) { // é vendedor
            $total = 0.00;
            $vendas_aux = $this->getVendasPeriodo(auth()->user()->id, $data['data_inicial'], $data['data_final'])->get();
            foreach ($vendas_aux as $v) {
                $qtdVendas = $v->qtd;
                $totalVendas += $v->total;
                $totalDescontos += $v->desconto;
                $totalAcrescimos += $v->acrescimo;
                $total += $v->total;
                $vendas[$v->ano . '-' . $v->mes][] = [
                    'user' => auth()->user(),
                    'total' => $total
                ];
            }
            $nomes[] = auth()->user()->name;
            $metas[] = auth()->user()->meta;
        } else { // é administrador
            if (isset($data['vendedor']) and $data['vendedor'])
                $users = User::where('id', $data['vendedor']);
            else
                $users = User::where('user_id', auth()->id())->orWhere('id', auth()->id());
            foreach ($users->get() as $u) {
                if (!$u->meta)
                    continue;
                $total = 0.00;
                $vendas_aux = $this->getVendasPeriodo($u->id, $data['data_inicial'], $data['data_final'])->get();
                foreach ($vendas_aux as $v) {
                    $qtdVendas = $v->qtd;
                    $totalVendas += $v->total;
                    $totalDescontos += $v->desconto;
                    $totalAcrescimos += $v->acrescimo;
                    $total += $v->total;
                    $vendas[$v->ano . '-' . $v->mes][] = [
                        'user' => $u,
                        'total' => $total
                    ];
                }
                $nomes[] = $u->name;
                $metas[] = $u->meta;
            }
        }
        return view('home', [
            'qtdVendas' => $qtdVendas,
            'totalVendas' => 'R$ ' . number_format($totalVendas, 2, ',', '.'),
            'totalDescontos' => 'R$ ' . number_format($totalDescontos, 2, ',', '.'),
            'totalAcrescimos' => 'R$ ' . number_format($totalAcrescimos, 2, ',', '.'),
            'chart' => json_encode($this->chart($nomes, $metas, $vendas)),
            'data_inicial' => $data['data_inicial'],
            'data_final' => $data['data_final'],
            'vendedores' => !auth()->user()->user_id ? auth()->user()->users()->orWhere('id', auth()->id())->get() : null,
            'vendedor' => $data['vendedor'] ?? null
        ]);
    }

    private function chart($nomes, $metas, $vendas)
    {
        $datasets[] = [
            'label' => 'Meta mensal',
            'data' => $metas,
            'borderColor' => 'rgba(0, 0, 0)',
            'type' => 'line',
            'order' => 1,
            'tension' => 0.0,
            'fill' => false,
        ];

        foreach ($vendas as $index => $v) {
            $vendas_aux = [];
            for ($i = 0; $i < count($v); $i++) {
                $vendas_aux[] = (float)$v[$i]['total'];
            }
            $cor = $this->getColor((int)$index);
            $r = $cor[0];
            $g = $cor[1];
            $b = $cor[2];
            $text_label = date('m/Y', strtotime($index . '-01'));
            $datasets[] = [
                'type' => 'bar',
                'label' => 'Vendas(' . $text_label . ')',
                'data' => $vendas_aux,
                'order' => 2,
                'backgroundColor' => [
                    "rgba($r, $g, $b, .3)",
                ],
                'borderColor' => [
                    "rgba($r, $g, $b, 1)",
                ],
                'borderWidth' => 2
            ];
        }
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $nomes,
                'datasets' => $datasets
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Meta x Resultado (Por funcionário)'
                    ],
                ],
                'responsive' => true,
//                'scales' => [
//                    'x' => [
//                        'stacked' => true
//                    ],
//                    'y' => [
//                        'title' => 'Valor em R$'
//                    ],
//                ]
            ]
        ];
    }

    private function getVendasPeriodo($user_id, $data_inicial, $data_final)
    {
        return Venda::select(
            DB::raw('sum(total) as total'),
            DB::raw('sum(desconto) as desconto'),
            DB::raw('sum(acrescimo) as acrescimo'),
            DB::raw('count(*) as qtd'),
            DB::raw('extract(month from created_at) as mes'),
            DB::raw('extract(year from created_at) as ano')
        )->whereBetween('created_at', [
            $data_inicial, $data_final
        ])->groupBy('mes', 'ano')->where('user_id', $user_id);
    }

    private function getColor($num)
    {
        $hash = md5('color' . $num); // modify 'color' to get a different palette
        return array(
            hexdec(substr($hash, 0, 2)), // r
            hexdec(substr($hash, 2, 2)), // g
            hexdec(substr($hash, 4, 2))); //b
    }

}
