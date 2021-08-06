@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        @if(session('status_error'))
        <div class="col-12">
            <div class="alert alert-danger">
                {{ session('status_error') }}
            </div>
        </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 text-left">
                            {{ __('Pedidos') }}
                        </div>
                        <div class="col-6 text-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('vendas.create') }}">Cadastrar pedido</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-12 col-sm-6 form-group">
                                @if(Agent::isMobile())<label>Pesquisar</label>@endif
                                <input type="search" name="q" class="form-control" placeholder="Pesquisar pedido"
                                        value="{{ $q }}" @if(!$q) required @endif>
                            </div>
                            <div class="col-12 col-sm-3 form-group">
                                @if(Agent::isMobile())<label>Período inicial</label>@endif
                                <input type="date" name="data_inicial" class="form-control" value="{{ $data_inicial }}" data-toggle="tooltip" ref="tooltip" title="Selecione o período" onchange="$(this).parents('form').submit()" placeholder="Selecione o período">
                            </div>
                            <div class="col-12 col-sm-3 form-group">
                                @if(Agent::isMobile())<label>Período final</label>@endif
                                <input type="date" name="data_final" class="form-control" value="{{ $data_final }}"  data-toggle="tooltip" ref="tooltip" title="Selecione o período" onchange="$(this).parents('form').submit()">
                            </div>
                        </div>
                    </form>
                    @if(count($vendas))
                        <ul class="list-group list-group-flush">
                            @foreach($vendas as $v)
                                <li class="list-group-item" style="padding: .75rem 0.25rem !important;">
                                    <div class="row">
                                        <div class="col-10">
                                            <div class="row">
                                                <div class="col-10">
                                                    <h6 class="mb-1 font-weight-bold text-uppercase">
                                                        {{ $v->cliente()->first()->nome }}
                                                    </h6>
                                                </div>
                                                <div class="col-2">
                                                    <span class="float-right"><b>Pedido: </b><span>{{ $v->iderp ?? 'Pendente' }}</span></span>
                                                </div>
                                                @if(Agent::isMobile())
                                                    <div class="col-12">
                                                        @if($v->produtos()->count())
                                                            <span
                                                                class="text-success">{{ $v->produtos()->sum('quantidade') }} @if((int)$v->produtos()->sum('quantidade') > 1)
                                                                    volumes @else
                                                                    volume @endif x R$ {{ number_format($v->produtos()->sum('total'), 2, ',', '.') }}</span> @if($v->desconto > 0.00)
                                                                <span
                                                                    class="text-danger"> - R${{ number_format($v->desconto, 2, ',', '.') }}</span> @endif
                                                        @else
                                                            <span>Nenhum produto no pedido</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="col-10">
                                                        @if($v->produtos()->count())
                                                            <span
                                                                class="text-success">{{ $v->produtos()->sum('quantidade') }} @if((int)$v->produtos()->sum('quantidade') > 1)
                                                                    volumes @else
                                                                    volume @endif x R$ {{ number_format($v->produtos()->sum('total'), 2, ',', '.') }}</span> @if($v->desconto > 0.00)
                                                                <span
                                                                    class="text-danger"> - R${{ number_format($v->desconto, 2, ',', '.') }}</span> @endif
                                                        @else
                                                            <span>Nenhum produto no pedido</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-2">
                                                        <span
                                                            class="float-right"><b>Data:</b> {{ date('d/m/Y', strtotime($v->created_at)) }}</span>
                                                    </div>
                                                @endif
                                                    <div class="col-12">
                                                        <b class="text-danger">R$ {{ number_format($v->total, 2, ',', '.') }}</b>
                                                        - <b
                                                            class="text-uppercase">{{ $v->tipoVenda()->first()->nome }}</b> @if(!auth()->user()->user_id and !Agent::isMobile())
                                                            <span class="float-right"><b>Vendedor: </b><span
                                                                    class="text-uppercase">{{ $v->vendedor()->first()->name }}</span></span>@else
                                                            <span
                                                                class="float-right">{{ date('d/m/Y', strtotime($v->created_at)) }}</span> @endif
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
{{--                                            @if(Agent::isMobile())--}}
                                                <div class="btn-group dropleft float-right">
                                                    <button type="button" class="btn btn-primary @if(Agent::isMobile()) btn-sm @endif dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: .6575rem;">
                                                        @if(!Agent::isMobile())
                                                        Opções
                                                        @endif
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item"
                                                           @if($v->iderp) href="javascript:void(0)" onclick="alert('Venda importada no ERP, não será possível alterar');" @else href="{{ route('vendas.edit', ['venda' => $v->id]) }}" @endif
                                                        >Gerenciar</a>
                                                        <a class="dropdown-item" href="{{ route('vendas.show', ['venda' => $v]) }}" target="_blank">Imprimir pedido</a>
                                                        <a class="dropdown-item" href="{{ route('vendas.show', ['venda' => $v, 'pdf' => 1]) }}" target="_blank">Gerar PDF do pedido</a>
                                                        <div class="dropdown-divider"></div>
                                                        <form method="post"
                                                              action="{{ route('vendas.destroy', ['venda' => $v->id]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                     onclick="
                                                                    @if($v->iderp)
                                                                        alert('Venda importada no ERP, não será possível excluir');
                                                                        return false;
                                                                    @else
                                                                        return confirm('Confirma a exclusão desta venda?');
                                                                    @endif">
                                                                Excluir
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
{{--                                            @else--}}
{{--                                                <div class="float-right">--}}
{{--                                                    <a class="btn btn-outline-primary btn-sm"--}}
{{--                                                       @if($v->iderp) href="javascript:void(0)" onclick="alert('Venda importada no ERP, não será possível alterar');" @else href="{{ route('vendas.edit', ['venda' => $v->id]) }}" @endif>Gerenciar</a>--}}
{{--                                                    <form method="post"--}}
{{--                                                          action="{{ route('vendas.destroy', ['venda' => $v->id]) }}"--}}
{{--                                                          class="d-inline-block">--}}
{{--                                                        @csrf--}}
{{--                                                        @method('DELETE')--}}
{{--                                                        <button type="submit" class="btn btn-sm btn-outline-danger"--}}
{{--                                                                onclick="--}}
{{--                                                                @if($v->iderp)--}}
{{--                                                                    alert('Venda importada no ERP, não será possível excluir');--}}
{{--                                                                    return false;--}}
{{--                                                                @else--}}
{{--                                                                    return confirm('Confirma a exclusão desta venda?');--}}
{{--                                                                @endif">--}}
{{--                                                            Excluir--}}
{{--                                                        </button>--}}
{{--                                                    </form>--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        {{ $vendas->appends(request()->except('page'))->links() }}
                    @else
                        <div class="alert alert-info">
                            Nenhum pedido encontrado
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
