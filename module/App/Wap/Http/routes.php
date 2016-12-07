<?php

Route::group(['middleware' => 'web', 'prefix' => 'wap', 'namespace' => 'App\Wap\Http\Controllers'], function()
{
	Route::get('/', 'WapController@index');
});