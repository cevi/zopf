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
    $actions = \App\Models\Action::where('action_status_id','=',config('status.action_abgeschlossen'))->where('total_amount','>',0);
    $action_counter = $actions->get()->count();
    $total_amount = $actions->sum('total_amount');
    return view('welcome', compact('action_counter','total_amount'));
});


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Auth::routes(['verify' => true]);

Route::group(['middleware' => 'verified'], function(){


    Route::get('/home', 'HomeController@index')->name('home');


    Route::resource('/groups', 'GroupsController', [ 'as' => 'home'])->only(['create', 'store', 'update', 'edit']);
    Route::put('groups/updateGroup/{group}',['as'=>'home.groups.updateGroup','uses'=>'GroupsController@updateGroup']);
    Route::get('orders/createDataTables', ['as'=>'orders.CreateDataTables','uses'=>'AdminOrdersController@createDataTables']);
    Route::put('actions/updateAction/{action}',['as'=>'admin.actions.updateAction','uses'=>'AdminActionsController@updateAction']);

    Route::get('routes/createDataTables', ['as'=>'routes.CreateDataTables','uses'=>'AdminRoutesController@createDataTables']);
    Route::get('routes/createModalDataTables', ['as'=>'routes.CreateModalDataTables','uses'=>'AdminRoutesController@CreateModalDataTables']);
    Route::get('users/createDataTables', ['as'=>'users.CreateDataTables','uses'=>'AdminUsersController@createDataTables']);
    Route::get('actions/createDataTables', ['as'=>'actions.CreateDataTables','uses'=>'AdminActionsController@createDataTables']);
    Route::get('groups/createDataTables', ['as'=>'groups.CreateDataTables','uses'=>'AdminGroupsController@createDataTables']);
    Route::get('orders/createDataTables', ['as'=>'orders.CreateDataTables','uses'=>'AdminOrdersController@createDataTables']);

    Route::get('/routes/{id}', ['as'=>'home.routes','uses'=>'HomeController@routes']);
    Route::get('/maps/{id}', ['as'=>'home.maps','uses'=>'HomeController@maps']);
    Route::get('/routes/order/{id}/delivered', ['as'=>'home.delivered','uses'=>'HomeController@delivered']);
    Route::get('/routes/order/{id}/deposited', ['as'=>'home.deposited','uses'=>'HomeController@deposited']);


    Route::get('/user/{user}', ['as'=>'home.user', 'uses'=>'UsersController@index']);
    Route::patch('/user/{user}', ['as'=>'home.update', 'uses'=>'UsersController@update']);

    Route::resource('admin/actions', 'AdminActionsController');

    Route::get('admin/users/searchajaxuser', ['as'=>'searchajaxuser','uses'=>'AdminUsersController@searchResponseUser']);

    Route::group(['middleware' => 'groupleader'], function(){

        Route::get('/admin',['as'=>'admin.index','uses'=>'AdminController@index']);
        Route::post('admin/log/create', ['as'=>'admin.logcreate','uses'=>'AdminController@logcreate']);

        Route::resource('admin/users', 'AdminUsersController');
        Route::post('admin/users/add',  ['as'=>'users.add', 'uses'=>'AdminUsersController@add']);

        Route::get('admin/actions/complete/{id}', ['as'=>'actions.complete','uses'=>'AdminActionsController@complete']);


        Route::get('admin/routes/{id}/overview', ['as'=>'routes.overview','uses'=>'AdminRoutesController@overview']);
        Route::get('admin/routes/{id}/downloadPDF', ['as'=>'routes.downloadPDF','uses'=>'AdminRoutesController@downloadPDF']);
        Route::get('admin/routes/map', ['as'=>'routes.map','uses'=>'AdminRoutesController@map']);
        Route::get('admin/routes/mapfilter', ['as'=>'routes.mapfilter','uses'=>'AdminRoutesController@mapfilter']);
        Route::post('admin/routes/{id}/send', ['as'=>'routes.send','uses'=>'AdminRoutesController@send']);
        Route::post('admin/routes/AssignOrders', ['as'=>'routes.AssignOrders','uses'=>'AdminRoutesController@AssignOrders']);
        Route::patch('admin/routes/RemoveOrder/{id}', ['as'=>'routes.RemoveOrder','uses'=>'AdminRoutesController@RemoveOrder']);
        Route::resource('admin/routes', 'AdminRoutesController');

        Route::get('admin/orders/map', ['as'=>'orders.map','uses'=>'AdminOrdersController@map']);
        Route::get('admin/orders/mapfilter', ['as'=>'orders.mapfilter','uses'=>'AdminOrdersController@mapfilter']);
        Route::post('admin/orders/createRoute', ['as'=>'orders.createRoute','uses'=>'AdminOrdersController@createRoute']);
        Route::post('admin/orders/uploadFile', 'AdminOrdersController@uploadFile');
        Route::post('admin/orders/pickup/{id}', ['as'=>'orders.pickup','uses'=>'AdminOrdersController@pickup']);
        Route::resource('admin/orders', 'AdminOrdersController');

        Route::resource('admin/addresses', 'AdminAddressesController');
        Route::resource('admin/logbooks', 'AdminLogbookController');
        Route::resource('admin/progress', 'AdminBakeryProgressController');


        // Route::get('admin/orders/bulkdelete', ['as'=>'orders.bulkdelete','uses'=>'AdminOrdersController@bulkdelete']);

        // Route::get('admin/searchajaxcity', ['as'=>'searchajaxcity','uses'=>'AdminAddressesController@searchResponseCity']);

        // Route::get('admin/searchajaxaddress', ['as'=>'searchajaxaddress','uses'=>'AdminOrdersController@searchResponseAddress']);

    });
});

Route::group(['middleware' => 'admin'], function(){
    Route::resource('admin/groups', 'AdminGroupsController');
});

Route::get('admin/run-migrations', function () {
    return Artisan::call('migrate', ["--force" => true ]);
});

Route::get('admin/run-migrations-seed', function () {
    return Artisan::call('migrate', ["--force" => true, "--seed" => true ]);
});

Route::get('admin/run-deployment', function () {
    echo 'config:cache <br>';
    Artisan::call('config:cache');
    echo 'view:cache <br>';
    Artisan::call('view:cache');
    return true;
});
