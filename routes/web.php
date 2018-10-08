<?php

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
    return view('welcome');
});
Route::name('busca.criar')->get('/busca/criar', ['as' => 'get', 'uses' => 'PrefixosController@criarBusca']);
Route::name('busca.fila')->get('/busca/fila', ['as' => 'get', 'uses' => 'PrefixosController@percorreFila']);
Route::name('busca.fila.engine')->get('/busca/fila/engine/{engine_id}', ['as' => 'get', 'uses' => 'PrefixosController@percorreFila']);
Route::name('busca.status')->get('/busca/{busca_id}/status', ['as' => 'get', 'uses' => 'PrefixosController@statusBusca']);
