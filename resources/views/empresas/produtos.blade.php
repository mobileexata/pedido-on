@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <span>{{ __('Produtos') }}</span>
                        <span class="float-right">
                        <div class="btn-group" role="group" aria-label="{{ __('Gerar PDF') }}">
                            <div class="btn-group" role="group">
                                <button id="drop-down-pdf" type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __('Gerar PDF') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="drop-down-pdf">
                                    <a class="dropdown-item" href="{{ route('empresas.produtos.pdf', array_merge(request()->except('page'), ['empresa' => $empresa, 'estoque' => 1])) }}" target="_blank">Com informação de estoque</a>
                                    <a class="dropdown-item" href="{{ route('empresas.produtos.pdf', array_merge(request()->except('page'), ['empresa' => $empresa, 'estoque' => 2])) }}" target="_blank">Sem informação de estoque</a>
                                    <a class="dropdown-item" href="{{ route('empresas.fabricantes.pdf', array_merge(request()->except('page'), ['empresa' => $empresa, 'estoque' => 1])) }}" target="_blank">Agrupado por fabricante com informação de estoque</a>
                                    <a class="dropdown-item" href="{{ route('empresas.fabricantes.pdf', array_merge(request()->except('page'), ['empresa' => $empresa, 'estoque' => 2])) }}" target="_blank">Agrupado por fabricante sem informação de estoque</a>
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label>Pesquisar produto</label>
                                    <input type="search" name="q" class="form-control" value="{{ $q }}"
                                        @if (!$q) required @endif>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-3 form-group">
                                    <label>Filtro de foto</label>
                                    <select class="form-control" aria-label="Default select example" name="has_photo"
                                        onchange="$(this).parents('form').submit()">
                                        <option @if ($has_photo != 'S' && $has_photo != 'N') selected @endif value="">Todos
                                        </option>
                                        <option value="S" @if ($has_photo == 'S') selected @endif>Produtos
                                            com foto</option>
                                        <option value="N" @if ($has_photo == 'N') selected @endif>Produtos
                                            sem foto</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-3 form-group">
                                    <label>Filtro de estoque</label>
                                    <select class="form-control" name="has_stock"
                                        onchange="$(this).parents('form').submit()">
                                        <option @if ($has_stock != 'S' && $has_stock != 'N') selected @endif value="">Todos
                                        </option>
                                        <option value="S" @if ($has_stock == 'S') selected @endif>Produtos
                                            com estoque</option>
                                        <option value="N" @if ($has_stock == 'N') selected @endif>Produtos
                                            sem estoque</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-3 form-group">
                                    <label>Filtro de fabricante</label>
                                    <select class="form-control" name="fabricante"
                                        onchange="$(this).parents('form').submit()">
                                        <option value="">Todos</option>
                                        @foreach ($fabricantes as $fabricante)
                                            <option value="{{ $fabricante->id }}"
                                                @if (request()->get('fabricante') == $fabricante->id) selected @endif>{{ $fabricante->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-3 form-group">
                                    <label>Ordem</label>
                                    <select class="form-control" name="order"
                                        onchange="$(this).parents('form').submit()">
                                        @foreach ($orders as $order_id => $order)
                                            <option value="{{ $order_id }}"
                                                @if (request()->get('order') == $order_id) selected @endif>{{ $order['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                        @if (count($produtos))
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-2 col-sm-1"></div>
                                        <div class="col-10 col-sm-5">
                                            <b>PRODUTO</b>
                                        </div>
                                        <div class="col-4 col-sm-2">
                                            <b>REF.</b>
                                        </div>
                                        <div class="col-4 col-sm-1">
                                            <b class="float-right">ESTOQUE</b>
                                        </div>
                                        <div class="col-4 col-sm-2">
                                            <b class="float-right">PREÇO</b>
                                        </div>
                                    </div>
                                </li>
                                @foreach ($produtos as $p)
                                    <li
                                        @if ($p->ativo == 'N') class="list-group-item text-danger" data-toggle="tooltip" rel="tooltip" title="Produto inativo" @else class="list-group-item" @endif>
                                        <div class="row">
                                            <div class="col-2 col-sm-1">
                                                <img src="@if ($p->imagem) {{ asset('produtos/' . $p->imagem) }}@else {{ asset('images/no_photo.png') }} @endif"
                                                    class="rounded" style="width: 50px; max-height: 100px">
                                            </div>
                                            <div class="col-10 col-sm-5">
                                                <h6>{{ $p->iderp }} - {{ $p->nome }}</h6>
                                            </div>
                                            <div class="col-4 col-sm-2">
                                                <span>{{ $p->referencia }}</span>
                                            </div>
                                            <div class="col-4 col-sm-1">
                                                <span
                                                    class="float-right @if ((float) $p->estoque < 0.0) text-danger @endif">{{ number_format($p->estoque, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-4 col-sm-2">
                                                <b class="float-right">R$ {{ number_format($p->preco, 2, ',', '.') }}</b>
                                            </div>
                                            @if (!Agent::isMobile())
                                                <div class="col-2 col-sm-1">
                                                    <a class="btn btn-sm btn-primary float-right" data-toggle="tooltip"
                                                        rel="tooltip" title="Atualizar imagem"
                                                        href="{{ route('produtos.edit', ['produto' => $p]) }}"><img
                                                            src="{{ asset('images/camera-64x64b.png') }}" width="20"
                                                            height="20"></a>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            {{ $produtos->appends(request()->except('page'))->links() }}
                        @else
                            <div class="alert alert-info">
                                Nenhum produto disponível
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAtualizarImagem" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="modalAtualizarImagemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAtualizarImagemLabel">Atualizar imagem</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    @push('js')
        <script>
            window.onload = function() {
                $(document).on('change', 'input:file[name="imagem"]', function(e) {
                    var input = $(this);
                    input.next('label').text(e.target.files[0] ? e.target.files[0].name : 'Selecione a imagem');
                });
            }
        </script>
    @endpush
@endsection
