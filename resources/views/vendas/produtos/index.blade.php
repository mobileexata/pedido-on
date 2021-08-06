@if(count($produtos))
<ul class="list-group list-group-flush">
@foreach($produtos as $p)
    <li class="list-group-item">
        <div class="row">
            <div class="col-2 col-sm-1">
                <img src="@if($p->produto()->first()->imagem){{ asset('produtos/' . $p->produto()->first()->imagem) }}@else {{ asset('images/no_photo.png') }}@endif" class="rounded" style="width: 50px; max-height: 100px; margin-left: -10px;">
            </div>
            <div class="col-8 col-sm-9">
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-1 font-weight-bold">{{ $p->nome }}</h6>
                    </div>
                    <div class="col-12">
                        <span class="text-success">{{ number_format($p->quantidade, 0, ',', '.') }} x R$ {{ number_format($p->preco, 2, ',', '.') }}</span> @if($p->desconto > 0.00) <span class="text-danger"> - R${{ number_format($p->desconto, 2, ',', '.') }}</span> @endif
                    </div>
                    <div class="col-12">
                        <b class="text-danger">R$ {{ number_format($p->total, 2, ',', '.') }}</b>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="row">
                    <div class="col-12">
                        <form onsubmit="return deletaItem($(this))" class="float-right" style="margin-right: -1.2rem;" method="post" action="{{ route('produtos-vendas.destroy', ['produtos_venda' => $p->id]) }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="venda_id" value="{{ $p->venda_id }}">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente remover este produto do pedido?')">Remover</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </li>
@endforeach
</ul>
@else
<div class="alert alert-info">
    Nenhum produto encontrado neste pedido
</div>
@endif
