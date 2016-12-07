<?php

Route::group(['middleware' => 'web', 'prefix' => 'api', 'namespace' => 'App\Api\Http\Controllers'], function()
{
	Route::get('/', 'ApiController@index');
});