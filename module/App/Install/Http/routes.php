<?php

Route::group(['middleware' => 'web', 'prefix' => 'install', 'namespace' => 'App\Install\Http\Controllers'], function()
{
	Route::get('/', 'InstallController@index');
});