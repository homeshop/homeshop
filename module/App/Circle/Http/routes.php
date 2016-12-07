<?php

Route::group(['middleware' => 'web', 'prefix' => 'circle', 'namespace' => 'App\Circle\Http\Controllers'], function()
{
	Route::get('/', 'CircleController@index');
});