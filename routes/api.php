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

//US9
Route::/*middleware('auth:api','cook')->*/get('users/cook/{id}/orders', 'UserControllerAPI@cookOrders');
//US11
Route::middleware('auth:api')->put('order/{id}', 'OrderControllerAPI@update');
//US12
Route::middleware('auth:api', 'waiter')->get('tables', 'RestaurantTablesControllerAPI@mesasLivres');
Route::middleware('auth:api', 'waiter')->post('meals/create', 'MealControllerAPI@store');
//US13
Route::middleware('auth:api', 'waiter')->get('users/{id}/meals', 'UserControllerAPI@meals');
Route::middleware('auth:api', 'waiter')->get('dishes', 'ItemsControllerAPI@dishes');
Route::middleware('auth:api', 'waiter')->get('drinks', 'ItemsControllerAPI@drinks');
Route::middleware('auth:api', 'waiter')->post('orders/create', 'OrderControllerAPI@store');
//US14
Route::middleware('auth:api', 'waiter')->get('users/waiter/{id}/pendingOrders', 'UserControllerAPI@waiterPendingOrders');
//US15
Route::middleware('auth:api', 'waiter')->delete('orders/{id}', 'OrderControllerAPI@destroy');
//US14
Route::middleware('auth:api', 'waiter')->get('users/waiter/{id}/preparedOrders', 'UserControllerAPI@waiterPreparedOrders');
//US22
Route::get('cashier','UserControllerAPI@invoices');


Route::middleware('auth:api')->post('users/create', 'UserControllerAPI@store');
Route::middleware('auth:api')->get('users/{id}', 'UserControllerAPI@show');
Route::middleware('auth:api')->delete('users/{id}', 'UserControllerAPI@destroy');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
	
Route::middleware(['auth:api', 'manager'])->get('managers','ManagerControllerAPI@index');
    Route::middleware('auth:api', 'manager')->get('managers','ManagerControllerAPI@itemsDataTable');

});


// US28
Route::middleware(['auth:api', 'manager'])->get('manager/itemsDataTable','ManagerControllerAPI@itemsDataTable');
Route::middleware(['auth:api', 'manager'])->delete('manager/managerItemList/{id}', 'ManagerControllerAPI@destroyItem');
Route::middleware(['auth:api', 'manager'])->post('manager/editItem/{id}/uploadFile', 'ManagerControllerAPI@uploadFile');
Route::middleware(['auth:api', 'manager'])->put('manager/editItem/{id}', 'ManagerControllerAPI@update');
Route::middleware(['auth:api', 'manager'])->post('manager/createItem', 'ManagerControllerAPI@store');
Route::middleware(['auth:api', 'manager'])->get('manager/tablesDataTable','ManagerControllerAPI@tablesDataTable');
Route::middleware(['auth:api', 'manager'])->delete('manager/managerTableList/{id}', 'ManagerControllerAPI@destroyTable');
Route::middleware(['auth:api', 'manager'])->post('manager/createTable', 'ManagerControllerAPI@storeTable');

//US29
Route::middleware(['auth:api', 'manager'])->get('manager/usersDataTable','ManagerControllerAPI@userDataTable');
