<?php

Route::group(['middleware' => 'web', 'prefix' => 'delivery', 'namespace' => 'App\Delivery\Http\Controllers'], function()
{
	Route::get('/', 'DeliveryController@index');
});