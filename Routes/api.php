<?php

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

$route_prefix = config('module.Faq.route_prefix', '');
$route_url_prefix = $route_prefix ? $route_prefix . '/' : '';
$route_name_prefix = $route_prefix ? $route_prefix . '.' : '';

Route::prefix("{$route_url_prefix}faq")->name("api.{$route_name_prefix}faq.")->group(function () {
    Route::post('/faq', "FaqController@edit")->name('edit');
    Route::get('/faq', 'FaqController@items')->name('items');
    Route::get('/faq/{id}', 'FaqController@item')->where('id', '[0-9]+')->name('item');
    Route::post('/faq/delete', 'FaqController@delete')->name('delete');

    Route::get('/faq/group', 'FaqController@groupItems')->name('group.items');
    Route::post('/faq/group', 'FaqController@groupEdit')->name('group.edit');
    Route::post('/faq/group/delete', 'FaqController@groupDelete')->name('group.delete');
});
