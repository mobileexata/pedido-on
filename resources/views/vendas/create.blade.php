@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                @if(auth()->user()->empresas()->count())
                    <form class="card" action="{{ route('vendas.store') }}" method="post">
                        @csrf
                        <div class="card-header">
                            <div class="row">
                                <h5 class="col-md-6 col-xl-6 col-sm-12 col-lg-6 text-left">
                                    {{ __('Cadastrar pedido') }}
                                </h5>
                                <div class="col-md-6 col-xl-6 col-sm-12 col-lg-6 text-left">
                                    <div class="row">
                                        <div class="col-md-2 col-xl-2 col-sm-12 col-lg-2">
                                            <span>Empresa:</span>
                                        </div>
                                        <div class="col-md-10 col-xl-10 col-sm-12 col-lg-10 ">
                                            <select name="empresa_id"
                                                    class="select2 @error('empresa_id') is-invalid @enderror"
                                                    onchange="resetParamsVendas()" style="width: 100%;">
                                                @foreach(auth()->user()->empresas()->get() as $e)
                                                    <option value="{{ $e->id }}"
                                                            @if($e->id == old('empresa_id')) selected @endif>{{ $e->fantasia }}</option>
                                                @endforeach
                                            </select>
                                            @error('empresa_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-xl-6 col-sm-12 col-lg-6 form-group">
                                    <label for="cliente_id" id="label_cliente_id">
                                        Cliente
                                    </label>
                                    <select name="cliente_id"
                                            id="cliente_id"
                                            class="@error('cliente_id') is-invalid @enderror"
                                            style="width: 100%;"></select>
                                    @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-xl-6 col-sm-12 col-lg-6 form-group">
                                    <label for="tiposvenda_id">
                                        Forma de pagamento
                                    </label>
                                    <select name="tiposvenda_id"
                                            class="tiposvenda_id @error('tiposvenda_id') is-invalid @enderror"
                                            style="width: 100%;"></select>
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
                                              rows="4">{{ old('observacoes') }}</textarea>
                                    @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center form-group">
                                    <button type="submit" class="btn btn-primary">Iniciar pedido</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-danger">
                        Atenção, você não está habilitado a realizar pedidos para nenhuma empresa, entre em contato com
                        o administrador do sistema
                    </div>
                @endif
            </div>
        </div>
    </div>
    @push('js')
        <script>
            function resetParamsVendas() {
                $('select.tiposvenda_id').val('').trigger('change');
                $('select.cliente_id').val('').trigger('change');
            }

            window.onload = function () {
                @include('vendas.select_clientes')
            }
        </script>
    @endpush
@endsection
