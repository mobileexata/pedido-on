@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">{{ __('Meu usuário') }}</div>
                <div class="card-body">
                    <form action="{{ route('user.update') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-4 col-sm-12 col-lg-4 col-xl-4 form-group">
                                <label>Seu nome</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Nome" value="{{ Auth()->user()->name }}" name="name">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-2 col-sm-12 col-lg-2 col-xl-2 form-group">
                                <label>Seu código no ERP</label>
                                <input class="form-control @error('iderp') is-invalid @enderror" type="text" placeholder="Código no ERP" value="{{ Auth()->user()->iderp }}" name="iderp">
                                @error('iderp')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-2 col-sm-12 col-lg-2 col-xl-2 form-group">
                                <label>Meta mensal</label>
                                <input class="form-control @error('meta') is-invalid @enderror" type="text" placeholder="Meta mensal" value="{{ Auth()->user()->meta ? number_format(Auth()->user()->meta, 2, ',', '') : '0,00' }}" name="meta">
                                @error('meta')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4 col-sm-12 col-lg-4 col-xl-4 form-group">
                                <label>Seu email</label>
                                <input class="form-control" type="text" placeholder="Email" value="{{ Auth()->user()->email }}" readonly title="Não é possível editar o email cadastrado">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3 col-sm-12 col-lg-3 col-xl-3 form-group">
                                <button class="btn btn-primary btn-block" type="submit">
                                    {{ __('Alterar meus dados') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if(!auth()->user()->user_id)
        <div class="col-12">
            <div class="alert alert-primary">
                <form method="post" action="{{ route('user.update-token') }}">
                    @csrf
                    <div class="form-group col-12">
                        @if(Auth()->user()->user_token)
                            Seu token de acesso é: <b>{{ Auth()->user()->user_token }}</b>
                        @else
                            Atenção, o token de acesso não foi gerado!
                        @endif
                    </div>
                    <div class="form-group col-12">
                        <button type="submit" class="btn btn-primary">Clique aqui para gerar o token
                            @if(Auth()->user()->user_token) novamente @endif</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-header">{{ __('Alterar minha senha') }}</div>
                <div class="card-body">
                    <form action="{{ route('user.update.password') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label for="password" class="col-md-2 col-form-label">{{ __('Nova senha:') }}</label>
                            <div class="col-md-4">
                                <input id="new_password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Nova senha') }}">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-2 col-form-label">{{ __('Confirme a senha:') }}</label>
                            <div class="col-md-4">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirme a senha') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-12 col-lg-3 col-xl-3 form-group">
                                <button class="btn btn-primary btn-block" type="submit">
                                    {{ __('Alterar minha senha') }}
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
