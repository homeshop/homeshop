<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin', 'namespace' => 'App\Admin\Http\Controllers'], function()
{
	Route::get('/', 'AdminController@index');
});