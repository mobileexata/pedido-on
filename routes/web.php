<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('home');
    //    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::prefix('configuracoes')->name('user.')->group(function () {
        Route::get('', 'UserController@configuracoes')->name('config');
        Route::post('update', 'UserController@updateConfig')->name('update');
        Route::post('update-password', 'UserController@updatePassword')->name('update.password');
        Route::post('update-token', 'UserController@updateToken')->name('update-token');

        Route::prefix('vendedores')->name('vendedores.')->group(function () {
            Route::get('', 'UserController@index')->name('index');
            Route::get('create', 'UserController@create')->name('create');
            Route::get('{user}/edit', 'UserController@edit')->name('edit');
            Route::post('store', 'UserController@store')->name('store');
            Route::put('{user}/habilitar-custo', 'UserController@habilitarCusto')->name('enable-custo');
            Route::put('{user}/desabilitar-custo', 'UserController@desabilitarCusto')->name('disable-custo');
            Route::put('{user}', 'UserController@update')->name('update');
            Route::delete('{user}', 'UserController@destroy')->name('destroy');
        });
    });

    Route::prefix('empresas')->name('empresas.')->group(function () {
        Route::get('', 'EmpresaController@index')->name('index');
        Route::get('{empresa}/clientes', 'EmpresaController@clientes')->name('clientes');
        Route::get('{empresa}/formas-pagamento', 'EmpresaController@tiposVendas')->name('tiposvendas');
        Route::get('{empresa}/produtos', 'EmpresaController@produtos')->name('produtos');
        Route::get('{empresa}/produtos/pdf', 'EmpresaController@produtosPdf')->name('produtos.pdf');
        Route::get('{empresa}/produtos/fabricantes-pdf', 'EmpresaController@fabricantesProdutosPdf')->name('fabricantes.pdf');
        Route::get('{empresa}/fabricantes', 'EmpresaController@fabricantes')->name('fabricantes');
    });

    Route::resource('vendas', 'VendaController');
    Route::resource('produtos', 'ProdutoController')->except(['show', 'create', 'index', 'store', 'destroy']);
    Route::get('vendas-totalizacao/{venda}', 'VendaController@totalizacaoVenda')->name('vendas.totalizacao');
    Route::get('vendas-finalizacao/{venda}', 'VendaController@finalizacaoVenda')->name('vendas.finalizacao');
    Route::resource('produtos-vendas', 'ProdutoVendaController')->except(['show']);

    Route::prefix('pesquisa')->name('pesquisa.')->group(function () {
        Route::get('clientes/{empresa}', 'VendaController@clientes')->name('clientes');
        Route::get('produtos/{empresa}', 'VendaController@produtos')->name('produtos');
        Route::get('tipos-de-vendas/{empresa}', 'VendaController@tiposVendas')->name('tiposvendas');
    });
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/teste', 'TesteController@teste');

Route::prefix('api')->group(function () {
    Route::get('', function () {
        return response()->json(['mensagem' => 'Connected']);
    });
    Route::post('empresas/{token}', 'ApiController@empresas');
    Route::post('clientes/{token}', 'ApiController@clientes');
    Route::get('clientes/{token}', 'ApiController@clientesPendentes');
    Route::patch('clientes/{token}', 'ApiController@setClientesPendentes');
    Route::post('tiposvendas/{token}', 'ApiController@tiposVendas');
    Route::post('produtos/{token}', 'ApiController@produtos');
    Route::get('pedidos/{token}', 'ApiController@getPedidos');
    Route::post('pedidos/{token}', 'ApiController@setPedidos');
    Route::post('rotas/{token}', 'ApiController@rotas');
    Route::post('rotas-funcionarios/{token}', 'ApiController@rotasFuncionarios');
    Route::post('fabricantes/{token}', 'ApiController@fabricantes');
});
