<?php

namespace App\Http\Controllers;

use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use PDF;

use function PHPSTORM_META\type;

class EmpresaController extends Controller
{
    const ORDERS = [
        1 => [
            'column' => 'produtos.iderp',
            'text' => 'Cód. Produto (Menor/Maior)'
        ],
        2 => [
            'column' => 'produtos.iderp',
            'type' => 'desc',
            'text' => 'Cód. Produto (Maior/Menor)'
        ],
        3 => [
            'column' => 'produtos.nome',
            'text' => 'Produto (Ordem alfabética)'
        ],
        /*4 => [
            'column' => 'fabricantes.nome',
            'text' => 'Fabricante (Ordem alfabética)'
        ]*/
    ];
    private $e;

    public function index()
    {
        return view('empresas.index', ['empresas' => auth()->user()->empresas()->paginate()]);
    }

    public function clientes($empresa)
    {
        $data = request()->all();
        $e = $this->findEmpresa($empresa);
        $clientes = $e->clientes();
        if (auth()->user()->user_id and auth()->user()->rotas()->count())
            $clientes->whereIn('rota_id', [auth()->user()->rotas()->select('rota_id')->distinct()->get()]);
        if (isset($data['q']))
            $clientes->where(function ($query) use ($data) {
                $query->where('nome', 'like', "%{$data['q']}%")
                    ->orWhere('documento', 'like', "%{$data['q']}%");
            });
        return view('empresas.clientes', ['clientes' => $clientes->paginate(), 'q' => $data['q'] ?? null]);
    }

    public function produtos($empresa)
    {
        $data = request()->all();
        $produtos = $this->getProdutos($empresa, $data);
        return view('empresas.produtos', [
            'empresa' => $empresa,
            'produtos' => $produtos->paginate(),
            'q' => $data['q'] ?? null,
            'has_photo' => $data['has_photo'] ?? null,
            'has_stock' => $data['has_stock'] ?? null,
            'fabricantes' => $this->getfabricantes($empresa, $data)->get(),
            'orders' => self::ORDERS,
            'order' => $data['order'] ?? null
        ]);
    }

    public function fabricantes($empresa)
    {
        $data = request()->all();
        $fabricantes = $this->getFabricantes($empresa, $data);
        return view('empresas.fabricantes', [
            'empresa' => $empresa,
            'fabricantes' => $fabricantes->paginate(),
            'q' => $data['q'] ?? null
        ]);
    }

    private function getProdutos($empresa, $data)
    {
        $this->e = $this->findEmpresa($empresa);
        return $this->e
            ->produtos()
            ->when(isset($data['q']), function ($query) use ($data) {
                return $query->where(function ($query) use ($data) {
                    return $query->orWhere('nome', 'like', "%{$data['q']}%")
                        ->orWhere('referencia', 'like', "%{$data['q']}%")
                        ->orWhere('preco', 'like', str_replace(',', '.', "%{$data['q']}%"))
                        ->orWhere('estoque', 'like', str_replace(',', '.', "%{$data['q']}%"))
                        ->orWhere('iderp', 'like', "%{$data['q']}%");
                });
            })
            ->when(isset($data['has_photo']), function ($query) use ($data) {
                return $query->{$data['has_photo'] == 'N' ? 'whereNull' : 'whereNotNull'}('imagem');
            })
            ->when(isset($data['has_stock']), function ($query) use ($data) {
                return $query->where('estoque', $data['has_stock'] == 'S' ? '>' : '<=', 0.00);
            })
            ->when(isset($data['fabricante']), function ($query) use ($data) {
                return $query->where('fabricante_id', $data['fabricante']);
            })->when(isset($data['order']), function ($query) use ($data) {
                if (!isset(self::ORDERS[$data['order']])) {
                    $data['order'] = 1;
                } 
                return $query->orderBy(self::ORDERS[$data['order']]['column'], self::ORDERS[$data['order']]['type'] ?? 'asc');
            });
    }

    private function getfabricantes($empresa, $data)
    {
        $this->e = $this->findEmpresa($empresa);
        return $this->e
            ->fabricantes()
            ->when(isset($data['q']), function ($query) use ($data) {
                return $query->where('nome', 'like', "%{$data['q']}%");
            });
    }

    public function tiposVendas($empresa)
    {
        $data = request()->all();
        $e = $this->findEmpresa($empresa);
        $tiposVendas = $e->tiposVendas();
        if (isset($data['q']))
            $tiposVendas->where('nome', 'like', "%{$data['q']}%");
        return view('empresas.tiposvendas', ['tiposVendas' => $tiposVendas->paginate(), 'q' => $data['q'] ?? null]);
    }

    private function findEmpresa($empresa_id)
    {
        return Auth::user()->empresas()->findOrFail($empresa_id);
    }

    public function produtosPdf($empresa)
    {
        ini_set('max_execution_time', '3000');
        ini_set('memory_limit', '-1');
        ini_set("pcre.backtrack_limit", "5000000");
        $produtos = $this->getProdutos($empresa, request()->all())->where('ativo', 'S')->get();
        $view = view('empresas.pdfprodutos', [
            'produtos' => $produtos,
            'nome_empresa' => $this->e->razao,
            'estoque' => (int)request()->input('estoque', 1)
        ])->render();
        $pdf = PDF::loadHtml($view);
        return $pdf->stream('produtos-' . $this->e->fantasia . '-' . time() . '.pdf');
    }

    public function fabricantesProdutosPdf(Request $request, $empresa)
    {
        ini_set('max_execution_time', '3000');
        ini_set('memory_limit', '-1');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->e = $this->findEmpresa($empresa);
        $fabricantes = $this
            ->e
            ->fabricantes()
            ->when($request->get('fabricante'), function ($query) use ($request) {
                return $query->where('id', $request->get('fabricante'));
            })
            ->get()
            ->map(function ($fabricante) use ($request) {
                $query_string = $request->get('q');

                return [
                    "iderp" => $fabricante->iderp,
                    "nome" => $fabricante->nome,
                    "produtos" => $fabricante
                        ->produtos()
                        ->where('ativo', 'S')
                        ->when($query_string, function ($query) use ($query_string) {
                            return $query->where(function ($query) use ($query_string) {
                                return $query->orWhere('nome', 'like', "%$query_string%")
                                    ->orWhere('referencia', 'like', "%$query_string%")
                                    ->orWhere('preco', 'like', str_replace(',', '.', "%$query_string%"))
                                    ->orWhere('estoque', 'like', str_replace(',', '.', "%$query_string%"))
                                    ->orWhere('iderp', 'like', "%$query_string%");
                            });
                        })
                        ->when($request->get('has_photo', false), function ($query) use ($request) {
                            return $query->{$request->get('has_photo') == 'N' ? 'whereNull' : 'whereNotNull'}('imagem');
                        })
                        ->when($request->get('has_stock', false), function ($query) use ($request) {
                            return $query->where('estoque', $request->get('has_stock') == 'S' ? '>' : '<=', 0.00);
                        })->when($request->get('order', false), function ($query) use ($request) {
                            if (!isset(self::ORDERS[$request->get('order')])) {
                                return $query->orderBy(self::ORDERS[1]['column'], 'asc');
                            } 
                            return $query->orderBy(self::ORDERS[$request->get('order')]['column'], self::ORDERS[$request->get('order')]['type'] ?? 'asc');
                        })
                        ->get()
                ];
            });
        $view = view('empresas.pdffabricantes', [
            'fabricantes' => $fabricantes,
            'nome_empresa' => $this->e->razao,
            'estoque' => (int) $request->get('estoque', 1)
        ])->render();
        $pdf = PDF::loadHtml($view);
        return $pdf->stream('fabricantes-produtos-' . $this->e->fantasia . '-' . time() . '.pdf');
    }

}
