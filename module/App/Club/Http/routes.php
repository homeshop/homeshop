<?php

Route::group(['middleware' => 'web', 'prefix' => 'club', 'namespace' => 'App\Club\Http\Controllers'], function()
{
	Route::get('/', 'ClubController@index');
});