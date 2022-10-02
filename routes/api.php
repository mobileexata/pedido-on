<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v2')->middleware('api.v2')->group(function () {
    Route::get('', function () {
        return response()->json(['mensagem' => 'Connected']);
    });
    Route::post('empresas', 'ApiControllerV2@empresas');
    Route::post('clientes', 'ApiControllerV2@clientes');
    Route::post('tiposvendas', 'ApiControllerV2@tiposVendas');
    Route::post('produtos', 'ApiControllerV2@produtos');
    Route::get('pedidos', 'ApiControllerV2@getPedidos');
    Route::post('pedidos', 'ApiControllerV2@setPedidos');
    Route::post('rotas', 'ApiControllerV2@rotas');
    Route::post('rotas-funcionarios', 'ApiControllerV2@rotasFuncionarios');
    Route::post('fabricantes', 'ApiControllerV2@fabricantes');
});