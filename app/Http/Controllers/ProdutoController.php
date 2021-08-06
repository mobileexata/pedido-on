<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoRequest;
use App\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    public $dirImg = 'produtos' . DIRECTORY_SEPARATOR;
    public function edit(Produto $produto)
    {
        return view('produtos.edit', ['produto' => $produto]);
    }

    public function update(ProdutoRequest $request, Produto $produto)
    {
        $public_path = public_path('produtos') . DIRECTORY_SEPARATOR;
        $storage_path = storage_path('app' . DIRECTORY_SEPARATOR . 'produtos') . DIRECTORY_SEPARATOR;
        $imagem = $request->imagem;
        $imagem->store('produtos');
        $imgName = $imagem->hashName();
        if (is_file($public_path . $produto->imagem))
            unlink($public_path . $produto->imagem);

        $produto->imagem = $imgName;
        if (copy($storage_path . $imgName, $public_path . $imgName))
            Storage::delete($this->dirImg . $imgName);
        $produto->save();
        return redirect()->route('empresas.produtos', ['empresa' => $produto->empresa_id]);
    }

}
