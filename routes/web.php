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

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'groupleader'], function(){

    Route::get('/admin','AdminController@index');

    Route::resource('admin/users', 'AdminUsersController');

    Route::resource('admin/actions', 'AdminActionsController');
    Route::get('admin/actions/complete/{id}', ['as'=>'actions.complete','uses'=>'AdminActionsController@complete']);
    
    Route::get('admin/routes/{id}/overview', ['as'=>'routes.overview','uses'=>'AdminRoutesController@overview']);
    Route::get('admin/routes/{id}/downloadPDF', ['as'=>'routes.downloadPDF','uses'=>'AdminRoutesController@downloadPDF']);
    Route::get('admin/routes/map', ['as'=>'routes.map','uses'=>'AdminRoutesController@map']);
    Route::get('admin/routes/mapfilter', ['as'=>'routes.mapfilter','uses'=>'AdminRoutesController@mapfilter']);
    Route::post('admin/routes/{id}/send', ['as'=>'routes.send','uses'=>'AdminRoutesController@send']);
    Route::resource('admin/routes', 'AdminRoutesController');

    Route::get('admin/orders/map', ['as'=>'orders.map','uses'=>'AdminOrdersController@map']);
    Route::get('admin/orders/mapfilter', ['as'=>'orders.mapfilter','uses'=>'AdminOrdersController@mapfilter']);
    Route::post('admin/orders/createRoute', ['as'=>'orders.createRoute','uses'=>'AdminOrdersController@createRoute']);
    Route::resource('admin/orders', 'AdminOrdersController');

    Route::resource('admin/addresses', 'AdminAddressesController');

    // Route::get('admin/orders/bulkdelete', ['as'=>'orders.bulkdelete','uses'=>'AdminOrdersController@bulkdelete']);

    // Route::get('admin/searchajaxcity', ['as'=>'searchajaxcity','uses'=>'AdminAddressesController@searchResponseCity']);

    // Route::get('admin/searchajaxaddress', ['as'=>'searchajaxaddress','uses'=>'AdminOrdersController@searchResponseAddress']);

});

Route::group(['middleware' => 'admin'], function(){
    Route::resource('admin/groups', 'AdminGroupsController');
});

Route::get('users/createDataTables', ['as'=>'users.CreateDataTables','uses'=>'AdminUsersController@createDataTables']);
Route::get('actions/createDataTables', ['as'=>'actions.CreateDataTables','uses'=>'AdminActionsController@createDataTables']);
Route::get('groups/createDataTables', ['as'=>'groups.CreateDataTables','uses'=>'AdminGroupsController@createDataTables']);
Route::get('orders/createDataTables', ['as'=>'orders.CreateDataTables','uses'=>'AdminOrdersController@createDataTables']);
Route::get('routes/createDataTables', ['as'=>'routes.CreateDataTables','uses'=>'AdminRoutesController@createDataTables']);

