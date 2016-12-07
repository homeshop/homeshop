<?php

Route::group(['middleware' => 'web', 'prefix' => 'chat', 'namespace' => 'App\Chat\Http\Controllers'], function()
{
	Route::get('/', 'ChatController@index');
});