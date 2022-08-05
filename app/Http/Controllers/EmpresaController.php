<?php

namespace App\Http\Controllers;

use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use PDF;

class EmpresaController extends Controller
{

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
            'fabricantes' => $this->getfabricantes($empresa, $data)->get()
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
                        ->orWhere('estoque', 'like', str_replace(',', '.', "%{$data['q']}%"));
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
            'nome_empresa' => $this->e->razao
        ])->render();
        $pdf = PDF::loadHtml($view);
        return $pdf->stream('produtos-' . $this->e->fantasia . '-' . time() . '.pdf');
    }
}
