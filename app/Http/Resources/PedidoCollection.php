<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PedidoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'iderpempresa' => $this->empresa()->first()->iderp,
            'iderpcliente' => $this->cliente()->first()->iderp,
            'iderptipovenda' => $this->tipoVenda()->first()->iderp,
            'iderpvendedor' => $this->vendedor()->first()->iderp,
            'total' => $this->total,
            'desconto' => $this->desconto,
            'acrescimo' => $this->acrescimo,
            'dtcadastro' => $this->created_at,
            'observacoes' => $this->observacoes,
            'produtos' => ProdutoPedidoCollection::collection($this->produtos()->whereNull('iderp')->get())
        ];
    }
}
