<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->put('users/edit', 'UserControllerAPI@update');

Route::post('users/create', 'UserControllerAPI@creteUser');

Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');

Route::post('login', 'LoginControllerAPI@login');
Route::middleware('auth:api')->post('logout', 'LoginControllerAPI@logout');
Route::get('items', 'ItemsControllerAPI@index');

Route::middleware('auth:api')->get('users/{id}', 'UserControllerAPI@show');
Route::middleware('auth:api')->put('users/{id}', 'UserControllerAPI@update');
Route::middleware('auth:api')->put('users/{id}/password', 'UserControllerAPI@updatePassword');
Route::middleware('auth:api')->delete('users/{id}', 'UserControllerAPI@destroy');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
	
Route::middleware(['auth:api', 'manager'])->get('managers','ManagerControllerAPI@index');
});
