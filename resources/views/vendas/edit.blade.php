@extends('layouts.app')
@section('content')
    <div class="@if(!Agent::isMobile()) container @endif">
        <div class="card">
            @if(auth()->user()->empresas()->count())
                <div class="card-header">
                    <div class="row">
                        <h5 class="col-md-6 col-xl-6 col-sm-12 col-lg-6 text-left">
                            {{ __('Gerenciar pedido') }}
                        </h5>
                        <div class="col-md-6 col-xl-6 col-sm-12 col-lg-6 text-left">
                            <div class="row">
                                <div class="col-md-2 col-xl-2 col-sm-12 col-lg-2">
                                    <span>Empresa:</span>
                                </div>
                                <div class="col-md-10 col-xl-10 col-sm-12 col-lg-10 " data-placement="top"
                                     data-toggle="tooltip"
                                     title="Não é possível alterar a empresa de um pedido em andamento">
                                    <select name="empresa_id" class="select2" disabled style="width: 100%;">
                                        <option class="text-uppercase"
                                                value="{{ $venda->empresa()->first()->id }}">{{ $venda->empresa()->first()->fantasia }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formCadPedido" action="{{ route('vendas.update', ['venda' => $venda->id]) }}"
                          method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="empresa_id" value="{{ $venda->empresa_id }}">
                        <div class="row">
                            <div class="col-md-6 col-xl-6 col-sm-12 col-lg-6 form-group">
                                <label for="cliente_id" id="label_cliente_id">
                                    Cliente{{ $situacao_saldo_calc }}
                                </label>
                                <select name="cliente_id" id="cliente_id"
                                        class="@error('cliente_id') is-invalid @enderror"
                                        style="width: 100%;">
{{--                                    @foreach(auth()->user()->empresas()->findOrFail($venda->empresa_id)->clientes()->get() as $c)--}}
{{--                                        <option value="{{ $c->id }}"--}}
{{--                                                @if($venda->cliente_id == $c->id) selected @endif>{{ $c->nome }}</option>--}}
{{--                                    @endforeach--}}
                                </select>
                                @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-6 col-sm-12 col-lg-6 form-group">
                                <label for="tiposvenda_id">
                                    Forma de pagamento
                                </label>
                                <select name="tiposvenda_id" id="tiposvenda_id"
                                        class="select2 @error('tiposvenda_id') is-invalid @enderror"
                                        style="width: 100%;">
                                    @foreach(auth()->user()->empresas()->findOrFail($venda->empresa_id)->tiposVendas()->where('ativo', 'S')->get() as $t)
                                        <option value="{{ $t->id }}" @if($venda->tiposvenda_id == $t->id) selected @endif> {{ $t->nome }}</option>
                                    @endforeach
                                </select>
                                @error('tiposvenda_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 form-group">
                                <label for="observacoes">
                                    Observações
                                </label>
                                <textarea name="observacoes"
                                          class="@error('observacoes') is-invalid @enderror form-control"
                                          placeholder="Digite aqui as observações referentes ao pedido"
                                          rows="2">{{ $venda->observacoes }}</textarea>
                                @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row" id="totalizacao">
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <span>Adicionar produto no pedido</span>
                                </div>
                                <div class="card-body" id="formCadProdutoPedido"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="font-weight-bold">Produtos do pedido</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-1" id="produtosVendas"></div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center form-group">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#modalFinalizacao">
                                Finalizar pedido
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-danger">
                    Atenção, você não está habilitado a realizar pedidos para nenhuma empresa, entre em contato com o
                    administrador do sistema
                </div>
            @endif
        </div>
        <div class="modal fade" id="modalFinalizacao" tabindex="-1" aria-labelledby="modalFinalizacaoLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFinalizacaoLabel">Finalizar pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="finalizacao">
                        @include('vendas.finalizacao', ['venda' => $venda])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('js')
        <script>
            function loadFormCadProdutoPedido(scroll = false) {
                $('#formCadProdutoPedido').fadeOut('slow', function () {
                    $('#formCadProdutoPedido').load('{{ route('produtos-vendas.create', ['produtos_venda' => $venda->id]) }}', function () {
                        $('#formCadProdutoPedido').fadeIn(function () {
                            if (scroll)
                                $('html,body').animate({
                                    scrollTop: $('#formCadProdutoPedido').offset().top - 55
                                }, 'slow');
                        });
                        loadSelectProduto();
                        maske();
                    });
                });
            }

            function loadProdutoPedido(fade = false, scroll = false) {
                if (!fade)
                    $('#produtosVendas').load('{{ route('produtos-vendas.index', ['produtos_venda' => $venda->id]) }}');
                else
                    $('#produtosVendas').fadeOut('slow', function () {
                        $('#produtosVendas').load('{{ route('produtos-vendas.index', ['produtos_venda' => $venda->id]) }}', function () {
                            $('#produtosVendas').fadeIn(function () {
                                if (scroll)
                                    $('html,body').animate({
                                        scrollTop: $('#produtosVendas').offset().top - 55
                                    }, 'slow');
                            });
                        });
                    });
                totalizacao();
            }

            function loadSelectProduto() {
                $('select.produto_id').select2({
                    ajax: {
                        url: function () {
                            return $('meta[name="site-link"]').attr('content') + 'pesquisa/produtos/' + $('select[name="empresa_id"]').val() + '?tiposvenda_id=' + $('select[name="tiposvenda_id"]').val()
                        },
                        dataType: 'json',
                        delay: 250
                    },
                    language: "pt-BR",
                    placeholder: 'Selecione o produto',
                    templateResult: function (item) {
                        if (!item.id)
                            return $("<div class='row font-weight-bold'>" +
                                "<div class='col-2 col-sm-2'></div>" +
                                "<div class='col-10 col-sm-6'>" + 'Produto' + "</div>" +
                                @if(auth()->user()->showCusto())
                                "<div class='col-4 col-sm-2 text-right '>" + 'Custo' + "</div>" +
                                @endif
                                "<div class='col-4 col-sm-2 text-right '>" + 'Preço' + "</div>" +
                                "<div class='col-4 col-sm-2 text-right '>" + 'Estoque' + "</div>" +
                                "</div>");
                        return $("<div class='row'>" +
                            "<div class='col-2 col-sm-2'><img src='" + item.imagem + "' class='rounded' style='width: 40px; max-height: 100px'></div>" +
                            "<div class='col-10 col-sm-6'>" + item.iderp + "-" + item.nome + "</div>" +
                            @if(auth()->user()->showCusto())
                            "<div class='col-4 col-sm-2 text-right'>" + item.custo_formatado + "</div>" +
                            @endif
                            "<div class='col-4 col-sm-2 text-right'>" + item.preco_formatado + "</div>" +
                            "<div class='col-4 col-sm-2 text-right " + (item.estoque <= 0.00 ? 'text-danger' : 'text-dark') + "'>" + item.estoque_formatado + "</div>" +
                            "</div>");
                    },
                    templateSelection: function (item) {
                        if (item.id) {
                            $('input[name="nome"]').val(item.nome);
                            setItem(item.preco, item.estoque);
                            return $("<div class='row font-weight-bold ml-0'>" +
                                "<div class='12'>" + item.iderp + " - " + item.nome + "</div>" +
                                "</div>");
                        } else
                            $('input[name="nome"]').val('');
                        return $("<div class='row font-weight-bold'>" +
                            "<div class='col-12'>Selecione o produto</div>" +
                            "</div>");
                    }
                });
            }

            window.onload = function () {
                loadFormCadProdutoPedido();
                loadProdutoPedido(true);
                totalizacao();
                $('#modalFinalizacao').on('shown.bs.modal', function () {
                    var cliente_id = $("#cliente_id").val()
                    var tipo_venda_id = $("#tiposvenda_id").val()
                    var route = '{{ route('vendas.finalizacao', ['venda' => $venda->id]) }}?cliente_id=' + cliente_id + '&tiposvenda_id=' + tipo_venda_id
                    var $this = $(this);
                    $this.find('.select2').attr('disabled', 'disabled')
                    $this.find('#finalizacao').load(route, function () {
                        $this.find('.select2').removeAttr('disabled').select2();
                        maske();
                        $this.find('#finalizacao').show()
                    });
                });
                @include('vendas.select_clientes')
            };

            function addProdutoVenda(form) {
                if (!form.find('select[name="produto_id"]').val()) {
                    alert('Selecione o produto!');
                    return false;
                }
                // if (parseToFloat(form.find('input[name="estoque"]').val()) <= 0.00) {
                //     alert('Estoque insuficiente!');
                //     return false;
                // }
                if (parseToFloat(form.find('input[name="total"]').val()) <= 0.00) {
                    alert('Total inválido (' + $('input[name="total"]').val() + ')!');
                    return false;
                }
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        form.find('button[type="submit"]').html('<div class="spinner-border spinner-border-sm" role="status">\n' +
                            '</div> Carregando...');
                    },
                    success: function () {
                        loadFormCadProdutoPedido(true);
                        loadProdutoPedido(true);
                    },
                    complete: function () {
                        form.find('button[type="submit"]').html('Adicionar no pedido')
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
                return false;
            }

            function calculaTotal() {
                if (!$('select[name="produto_id"]').val())
                    $('input[name="total"]').val('0,00');
                else {
                    var preco = parseToFloat($('input[name="preco"]').val() ? $('input[name="preco"]').val() : '0,00');
                    var quantidade = parseToFloat($('input[name="quantidade"]').val() ? $('input[name="quantidade"]').val() : '1,00');
                    var desconto = parseToFloat($('input[name="desconto"]').val() ? $('input[name="desconto"]').val() : '0,00');
                    var acrescimo = parseToFloat($('input[name="acrescimo"]').val() ? $('input[name="acrescimo"]').val() : '0,00');
                    var total = (preco * quantidade) - desconto + acrescimo;
                    $('input[name="total"]').val(parseToInput(total.toFixed(2)));
                }
            }

            function parseToInput(value) {
                return value.toString().replace(',', '').replace('.', ',');
            }

            function parseToFloat(value) {
                return parseFloat(value.replace('.', '').replace(',', '.'));
            }

            function setItem(preco = '0,00', estoque = 0.00) {
                $('input[name="preco"]').val(parseToInput(preco));
                $('input[name="estoque"]').val(parseToInput(estoque));
                if (estoque <= 0.00)
                    $('input[name="estoque"]').removeClass('is-valid').addClass('is-invalid');
                else
                    $('input[name="estoque"]').removeClass('is-invalid').addClass('is-valid');
                $('input[name="quantidade"]').val('1,00');
                $('input[name="desconto"]').val('0,00');
                $('input[name="acrescimo"]').val('0,00');
                calculaTotal();
            }

            function deletaItem(form) {
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        form.find('button[type="submit"]').html('<div class="spinner-border spinner-border-sm" role="status">\n' +
                            '</div> Aguarde...');
                    },
                    success: function () {
                        form.parents('li[class="list-group-item"]').fadeOut('slow', function () {
                            setTimeout(function () {
                                loadProdutoPedido();
                            }, 500);

                        });
                        // alert('Produto excluido do pedido');
                    },
                    complete: function () {
                        form.find('button[type="submit"]').html('Excluir')
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
                return false;
            }

            function calculaTotalVenda($input = null) {
                var $continue = true;
                var total_venda = parseToFloat($('.formCadPedido input[name="total_calc"]').val());
                var acrescimo = parseToFloat($('.formCadPedido input[name="acrescimo"]').val() ? $('.formCadPedido input[name="acrescimo"]').val() : '0,00');
                var desconto = parseToFloat($('.formCadPedido input[name="desconto"]').val() ? $('.formCadPedido input[name="desconto"]').val() : '0,00');
                if ($input && $input.attr('name') == 'desconto')
                    if (desconto >= total_venda) {
                        $('.formCadPedido input[name="desconto"]').addClass('is-invalid').parent().find('.invalid-feedback').html('Desconto inválido');
                        $continue = false;
                    } else
                        $('.formCadPedido input[name="desconto"]').removeClass('is-invalid').parent().find('.invalid-feedback').html('');
                if ($continue) {
                    var total = total_venda + acrescimo - desconto;
                    $('.formCadPedido input[name="total"]').val(parseToInput(total.toFixed(2)));
                }
            }

            function calculaDescontoVenda() {
                var percentual = parseToFloat($('.formCadPedido input[name="desconto_percent"]').val());
                var total_venda = parseToFloat($('.formCadPedido input[name="total_calc"]').val());
                var desconto = percentual * total_venda / 100;
                if (desconto) {
                    $('.formCadPedido input[name="desconto"]').val(parseToInput(desconto.toFixed(2)));
                }
                $('.formCadPedido input[name="desconto_percent"]').val('0,00');
                calculaTotalVenda();
            }

            function calculaAcrescimoVenda() {
                var percentual = parseToFloat($('.formCadPedido input[name="acrescimo_percent"]').val());
                var total_venda = parseToFloat($('.formCadPedido input[name="total_calc"]').val());
                var acrescimo = percentual * total_venda / 100;
                if (acrescimo) {
                    $('.formCadPedido input[name="acrescimo"]').val(parseToInput(acrescimo.toFixed(2)));
                }
                $('.formCadPedido input[name="acrescimo_percent"]').val('0,00');
                calculaTotalVenda();
            }

            function totalizacao() {
                $('#totalizacao').load('{{ route('vendas.totalizacao', ['venda' => $venda->id]) }}');
            }

            function maske() {
                $('.money').mask('#.##0,00', {reverse: true}).change(function () {
                    if (!$(this).val())
                        $(this).val('0,00');
                }).focusin(function () {
                    $(this).select();
                });
                $('.money2').mask("#.##0,00", {reverse: true}).change(function () {
                    if (!$(this).val())
                        $(this).val('0,00');
                }).focusin(function () {
                    $(this).select();
                });
                $('.percent').mask('#.##0,00%', {reverse: true}).change(function () {
                    if (!$(this).val())
                        $(this).val('0,00%');
                }).focusin(function () {
                    $(this).select();
                });
            }
        </script>
    @endpush
@endsection
