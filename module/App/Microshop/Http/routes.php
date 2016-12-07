<?php

Route::group(['middleware' => 'web', 'prefix' => 'microshop', 'namespace' => 'App\Microshop\Http\Controllers'], function()
{
	Route::get('/', 'MicroshopController@index');
});