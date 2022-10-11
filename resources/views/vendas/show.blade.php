@extends('layouts.app', ['menu' => false])
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-6">
                <img src="{{ asset('images/icon.png') }}" width="25">
                {{ config('app.name', 'Laravel') }}
            </div>
            <div class="col-6 text-right">
                <p><b>Impresso em:</b> {{ date('d/m/Y H:i:s') }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-12">
                        <h1 class="text-uppercase text-center">
                            {{ $venda->empresa()->first()->razao }} - ({{ $venda->empresa()->first()->fantasia }})
                        </h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <h6><b>Pedido nº:</b> {{ $venda->iderp ? str_pad($venda->iderp, 10, '0', STR_PAD_LEFT) : 'Importação pendente' }}</h6>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6><b>Cliente:</b> {{ $venda->cliente()->first()->nome }}</h6>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <h6><b>Data do pedido:</b> {{ date('d/m/Y', strtotime($venda->created_at)) }}</h6>
                    </div>
                    <div class="col-12 col-sm-4">
                        <h6><b>Vendedor:</b> {{ $venda->vendedor()->first()->name }}</h6>
                    </div>
                    <div class="col-12 col-sm-4">
                        <h6><b>Forma de pagamento:</b> {{ $venda->tipoVenda()->first()->nome }}</h6>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <h6><b>Quantidade de produtos:</b> {{ $venda->produtos()->count() }}</h6>
                    </div>
                    <div class="col-12 col-sm-4">
                        <h6><b>Total dos produtos: </b>{{ number_format($venda->produtos()->sum('total'), 2, ',', '.') }}</h6>
                    </div>
                    <div class="col-6 col-sm-2">
                        <h6><b>Acréscimo: </b>{{ number_format($venda->acrescimo, 2, ',', '.') }}</h6>
                    </div>
                    <div class="col-6 col-sm-2">
                        <h6><b>Desconto: </b>{{ number_format($venda->desconto, 2, ',', '.') }}</h6>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="text-center text-danger"><b>Total do pedido:</b> {{ number_format($venda->total, 2, ',', '.') }}</h5>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row font-weight-bold">
                                    <div class="col-2 col-sm-1">
                                        &nbsp;
                                    </div>
                                    <div class="col-9 col-sm-4">
                                        Produto
                                    </div>
                                    <div class="col-6 col-sm-1 text-right">
                                        Qtd.
                                    </div>
                                    <div class="col-6 col-sm-2 text-right">
                                        Valor
                                    </div>
                                    <div class="col-6 col-sm-2 text-right">
                                        Acrésc/Desc.
                                    </div>
                                    <div class="col-6 col-sm-2 text-right">
                                        Total
                                    </div>
                                </div>
                            </li>
                            @foreach($venda->produtos()->get() as $p)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-2 col-sm-1">
                                            <img src="@if($p->produto()->first()->imagem){{ asset('produtos/' . $p->produto()->first()->imagem) }}@else {{ asset('images/no_photo.png') }}@endif" class="rounded" style="width: 50px; max-height: 100px; margin-left: -10px;">
                                        </div>
                                        <div class="col-9 col-sm-4">
                                            <h6 class="mb-1 font-weight-bold">{{ $p->produto()->first()->iderp }} - {{ $p->nome }}</h6>
                                        </div>
                                        <div class="col-6 col-sm-1 text-right">
                                            {{ number_format($p->quantidade, 0, ',', '.') }}
                                        </div>
                                        <div class="col-6 col-sm-2 text-right">
                                            {{ number_format($p->preco, 2, ',', '.') }}
                                        </div>
                                        <div class="col-6 col-sm-2 text-right">
                                            {{ number_format($p->desconto > 0.00 ? $p->desconto * -1 : $p->acrescimo, 2, ',', '.') }}
                                        </div>
                                        <div class="col-6 col-sm-2 text-right">
                                            <b class="text-danger">R$ {{ number_format($p->total, 2, ',', '.') }}</b>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ocultarNaImpressao">
        <button type="button" class="btn btn-primary" onclick="imprimir()" style="position:fixed;
            /*width:80px;*/
            height:60px;
            bottom:40px;
            right:40px;">
            Imprimir
        </button>
    </div>
@endsection
@push('js')
    <script>
        function imprimir()
        {
            $('.ocultarNaImpressao').hide();
            window.print();
            $('.ocultarNaImpressao').show();
        }
    </script>
@endpush
