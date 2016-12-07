<?php

Route::group(['middleware' => 'web', 'prefix' => 'other', 'namespace' => 'App\Other\Http\Controllers'], function()
{
	Route::get('/', 'OtherController@index');
});