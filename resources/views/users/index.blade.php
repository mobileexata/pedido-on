@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 text-left">
                                {{ __('Vendedores') }}
                            </div>
                            <div class="col-6 text-right">
                                <a class="btn btn-primary btn-sm" href="{{ route('user.vendedores.create') }}">Cadastrar vendedor</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-7 form-group">
                                    <input type="search" name="q" class="form-control" placeholder="Pesquisar vendedor" value="{{ $q }}" @if(!$q) required @endif>
                                </div>
                            </div>
                        </form>
                        @if(count($users))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Pedidos</th>
                                        <th>Meta (R$)</th>
                                        <th>Cadastrado em</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->vendas()->count() }}</td>
                                        <td>{{ number_format($u->meta, 2, ',', '.') }}</td>
                                        <td>{{ date('d/m/Y', strtotime($u->created_at)) }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('user.vendedores.edit', ['user' => $u->id]) }}">Alterar</a>
                                            <div style="display: inline-block;">
                                                <form method="post" action="{{ route('user.vendedores.destroy', ['user' => $u->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirma a exclusão deste usuário?')">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $users->appends(request()->except('page'))->links() }}
                        @else
                        <div class="alert alert-info">
                            Nenhum vendedor encontrado
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
