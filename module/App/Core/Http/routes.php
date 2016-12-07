<?php

Route::group(['middleware' => 'web', 'prefix' => 'core', 'namespace' => 'App\Core\Http\Controllers'], function()
{
	Route::get('/', 'CoreController@index');
});