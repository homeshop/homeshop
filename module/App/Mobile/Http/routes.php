<?php

Route::group(['middleware' => 'web', 'prefix' => 'mobile', 'namespace' => 'App\Mobile\Http\Controllers'], function()
{
	Route::get('/', 'MobileController@index');
});