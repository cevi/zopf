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

Route::group(['middleware' => 'admin'], function(){

    Route::get('/admin','AdminController@index');

    Route::resource('admin/users', 'AdminUsersController');

    Route::resource('admin/routes', 'AdminRoutesController');

    Route::resource('admin/actions', 'AdminActionsController');

    Route::resource('admin/commands', 'AdminCommandsController');

});

