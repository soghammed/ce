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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['auth:web'])->group(function(){

	//all clients
	Route::get('clients', 'App\Http\Controllers\ClientController@index');

	//new client
	Route::post('client', 'App\Http\Controllers\ClientController@store');

	//update client
	Route::post('clients', 'App\Http\Controllers\ClientController@store');

	//remove client
	Route::delete('client/{id}', 'App\Http\Controllers\ClientController@destroy');



	//all transactions
	Route::get('transactions', 'App\Http\Controllers\TransactionController@index');

	//client list
	Route::get('transaction/clients', 'App\Http\Controllers\TransactionController@clients');

	//new client
	Route::post('transaction', 'App\Http\Controllers\TransactionController@store');

	//update client
	Route::post('transactions', 'App\Http\Controllers\TransactionController@store');
	
	//delete client
	Route::delete('transaction/{id}', 'App\Http\Controllers\TransactionController@destroy');

});