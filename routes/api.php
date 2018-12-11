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

//US1
Route::get('items', 'ItemsControllerAPI@index');

Auth::routes(['verify' => true]);


//Route::put('users/{id}/password', 'UserControllerAPI@updatePassword');
//US3
Route::post('login', 'LoginControllerAPI@login');
Route::middleware('auth:api')->post('logout', 'LoginControllerAPI@logout');
Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');
//US4
Route::middleware('auth:api')->put('users/{id}/password', 'UserControllerAPI@updatePassword');
//US5
Route::middleware('auth:api')->put('users/{id}', 'UserControllerAPI@update');
Route::middleware('auth:api')->post('users/{id}/uploadFile', 'UserControllerAPI@uploadFile');

Route::middleware('auth:api')->post('users/create', 'UserControllerAPI@createUser');

Route::middleware('auth:api')->get('users/{id}', 'UserControllerAPI@show');
Route::middleware('auth:api')->delete('users/{id}', 'UserControllerAPI@destroy');

//US9
Route::get('users/{id}/orders', 'UserControllerAPI@orders');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
	
Route::middleware(['auth:api', 'manager'])->get('managers','ManagerControllerAPI@index');
});
