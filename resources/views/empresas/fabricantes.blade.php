@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <span>{{ __('Fabricantes') }}</span>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-12 col-sm-6 form-group">
                                    <label>Pesquisar fabricante</label>
                                    <input type="search" name="q" class="form-control" value="{{ $q }}"
                                        @if (!$q) required @endif>
                                </div>
                            </div>
                        </form>
                        @if (count($fabricantes))
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Fabricantes') }}</th>
                                            <th class="text-right">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fabricantes as $f)
                                            <tr>
                                                <td>{{ $f->nome }}</td>
                                                <td class="text-right" width="50">#</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $fabricantes->appends(request()->except('page'))->links() }}
                        @else
                            <div class="alert alert-info">
                                Nenhum fabricante disponível
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
