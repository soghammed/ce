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



// Route::middleware(['auth:web'])->group(function(){

	Route::get('clients', 'App\Http\Controllers\ClientController@index');
	Route::post('client', 'App\Http\Controllers\ClientController@store');
	Route::post('clients', 'App\Http\Controllers\ClientController@store');
	Route::delete('client/{id}', 'App\Http\Controllers\ClientController@destroy');

	Route::get('transactions', 'App\Http\Controllers\TransactionController@index');
	Route::get('transaction/clients', 'App\Http\Controllers\TransactionController@clients');
	Route::post('transaction', 'App\Http\Controllers\TransactionController@store');
	Route::post('transactions', 'App\Http\Controllers\TransactionController@store');
	Route::delete('transaction/{id}', 'App\Http\Controllers\TransactionController@destroy');

// });
