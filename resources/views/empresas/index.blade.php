@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">{{ __('Empresa') }}</div>
                    <div>
                        @if(count($empresas))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Razão Social</th>
                                        <th>Empresa</th>
                                        <th>CNPJ</th>
                                        <th class="text-right">Pedidos</th>
                                        <th class="text-right">Clientes</th>
                                        <th class="text-right">Produtos</th>
                                        <th class="text-center">Formas de pagamento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($empresas as $e)
                                    <tr>
                                        <td>{{ $e->razao }}</td>
                                        <td>{{ $e->fantasia }}</td>
                                        <td>{{ $e->cnpj }}</td>
                                        <td class="text-right" width="50">
                                            <a href="{{ route('vendas.index') }}"  title="Visualizar pedidos desta empresa" data-toggle="tooltip" data-placement="top">
                                                {{ $e->vendas()->count() }}
                                            </a>
                                        </td>
                                        <td class="text-right" width="50">
                                            <a href="{{ route('empresas.clientes', ['empresa' => $e->id]) }}"  title="Visualizar clientes desta empresa" data-toggle="tooltip" data-placement="top">
                                                {{ $e->clientes()->count() }}
                                            </a>
                                        </td>
                                        <td class="text-right" width="50">
                                            <a href="{{ route('empresas.produtos', ['empresa' => $e->id]) }}"  title="Visualizar produtos desta empresa" data-toggle="tooltip" data-placement="top">
                                                {{ $e->produtos()->count() }}
                                            </a>
                                        </td>
                                        <td class="text-right" width="100">
                                            <a href="{{ route('empresas.tiposvendas', ['empresa' => $e->id]) }}"  title="Visualizar formas de pagamento desta empresa" data-toggle="tooltip" data-placement="top">
                                                {{ $e->tiposVendas()->count() }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $empresas->appends(request()->except('page'))->links() }}
                        @else
                        <div class="alert alert-info">
                            Nenhuma empresa encontrada
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
