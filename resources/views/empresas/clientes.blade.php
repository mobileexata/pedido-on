@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mt-3">{{ __('Cliente') }}</h4>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-12 col-sm-7 form-group">
                                    <input type="search" name="q" class="form-control" placeholder="Pesquisar cliente"
                                           value="{{ $q }}" @if(!$q) required @endif>
                                </div>
                            </div>
                        </form>
                        @if(count($clientes))
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nome @if(Agent::isMobile())/<br>Situação @endif </th>
                                        <th>Documento @if(Agent::isMobile())/<br>Saldo pendente @endif </th>
                                        @if(!Agent::isMobile())
                                            <th class="text-center">Situação</th>
                                            <th class="text-right">Saldo pendente</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($clientes as $c)
                                        @php $situacao = $c->situacao ?? '-'; @endphp
                                        @php $saldo_pendente = $c->saldo_pendente ? ('R$' . number_format($c->saldo_pendente, 2, ',', '.')) : '-'; @endphp
                                        <tr @if($c->ativo == 'N') class="text-danger" data-toggle="tooltip"
                                            rel="tooltip" title="Cliente inativo" @endif>
                                            <td>{{ $c->nome }} @if(Agent::isMobile())<br><b>{{ $situacao }}</b>@endif</td>
                                            <td>{{ $c->documento }} @if(Agent::isMobile())
                                                    <br><b>{{ $saldo_pendente }}</b>@endif</td>
                                            @if(!Agent::isMobile())
                                                <td class="text-center">{{ $situacao }}</td>
                                                <td class="text-right">{{ $saldo_pendente }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $clientes->appends(request()->except('page'))->links() }}
                        @else
                            <div class="alert alert-info">
                                Nenhum cliente disponível
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
