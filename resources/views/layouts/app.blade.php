<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="site-link" content="{{ url('') }}/">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}"/>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/chart.min.js') }}"></script>
</head>
<body>
<div id="app">
    @if(!isset($menu))
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <button class="navbar-toggler float-left mr-3" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false"
                            aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('images/icon.png') }}" width="25">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    @auth
                        <li class="nav-item @if(Route::currentRouteName() == 'home') active @endif">
                            <a class="nav-link" href="{{ route('home') }}">{{ __('Dashboard') }}</a>
                        </li>
                        {{-- @if(!auth()->user()->user_id) --}}
                            <li class="nav-item @if(in_array(Route::currentRouteName(), ['empresas.index', 'empresas.clientes', 'empresas.produtos', 'empresas.tiposvendas'])) active @endif">
                                <a class="nav-link" href="{{ route('empresas.index') }}">{{ __('Empresas') }}</a>
                            </li>
                        {{-- @endif --}}
                        <li class="nav-item @if(in_array(Route::currentRouteName(), ['vendas.index', 'vendas.create', 'vendas.edit', 'vendas.show'])) active @endif">
                            <a class="nav-link" href="{{ route('vendas.index') }}">{{ __('Pedidos') }}</a>
                        </li>
                        @if(!auth()->user()->user_id)
                            <li class="nav-item @if(in_array(Route::currentRouteName(), ['user.vendedores.index', 'user.vendedores.create', 'user.vendedores.edit'])) active @endif">
                                <a class="nav-link"
                                   href="{{ route('user.vendedores.index') }}">{{ __('Vendedores') }}</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav ml-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown @if(Route::currentRouteName() == 'user.config') active @endif">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('user.config') }}">
                                    {{ __('Configurações') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Sair') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    @endif
    <main class="py-4">
        @yield('content')
    </main>
</div>
@stack('js')
</body>
</html>
