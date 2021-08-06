@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Alterar vendedor') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.vendedores.update', ['user' => $user->id]) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden">
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nome') }}</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required autocomplete="email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="iderp" class="col-md-4 col-form-label text-md-right">{{ __('Código do vendedor no ERP') }}</label>
                                <div class="col-md-6">
                                    <input id="iderp" type="text" class="form-control @error('iderp') is-invalid @enderror" name="iderp" value="{{ $user->iderp }}" required>
                                    @error('iderp')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
{{--                            @dd($user)--}}
                            <div class="form-group row">
                                <label for="meta" class="col-md-4 col-form-label text-md-right">{{ __('Meta (R$)') }}</label>
                                <div class="col-md-6">
                                    <input id="meta" type="text" class="form-control @error('meta') is-invalid @enderror" name="meta" value="{{ number_format($user->meta, 2, ',', '') }}" autocomplete="meta" autofocus>
                                    @error('meta')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Senha') }}</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirme a senha') }}</label>
                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="empresas" class="col-md-4 col-form-label text-md-right">{{ __('Empresas atendidas') }}</label>
                                <div class="col-md-6">
                                    <select id="empresas" class="form-control select2" name="empresas[]" multiple>
                                    @foreach($empresas as $e)
                                        <option value="{{ $e->id }}" @if($user->empresas()->where('empresa_user.empresa_id', $e->id)->count()) selected @endif>
                                            {{ $e->razao }} - {{ $e->cnpj }}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">{{ __('Rotas atendidas') }}</label>
                                <div class="col-md-6">
                                    <ul>
                                        @foreach($user->rotas()->get() as $r)
                                            <li><b>{{ $r->nome }}</b> ({{ $r->empresa()->first()->fantasia }})</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Salvar') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
