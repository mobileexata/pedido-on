@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mt-3">{{ __('Formas de pagamento') }}</h4>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-7 form-group">
                                    <input type="search" name="q" class="form-control" placeholder="Pesquisar forma de pagamento" value="{{ $q }}" @if(!$q) required @endif>
                                </div>
                            </div>
                        </form>
                        @if(count($tiposVendas))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Forma de pagamento</th>
                                    <th>Tipo de preço</th>
                                    <th class="text-right">#</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tiposVendas as $t)
                                    <tr @if($t->ativo == 'N') class="text-danger" data-toggle="tooltip" rel="tooltip" title="Forma de pagamento inativa" @endif>
                                        <td>{{ $t->nome }}</td>
                                        <td>{{ $t->desctipopreco }}</td>
                                        <td class="text-right" width="50">#</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $tiposVendas->appends(request()->except('page'))->links() }}
                        @else
                        <div class="alert alert-info">
                            Nenhuma forma de pagamento disponível
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
