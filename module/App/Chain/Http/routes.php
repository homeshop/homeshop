<?php

Route::group(['middleware' => 'web', 'prefix' => 'chain', 'namespace' => 'App\Chain\Http\Controllers'], function()
{
	Route::get('/', 'ChainController@index');
});