<?php

Route::group(['middleware' => 'web', 'prefix' => 'shop', 'namespace' => 'App\Shop\Http\Controllers'], function()
{
	Route::get('/', 'ShopController@index');
});