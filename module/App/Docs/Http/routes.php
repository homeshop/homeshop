<?php

Route::group(['middleware' => 'web', 'prefix' => 'docs', 'namespace' => 'App\Docs\Http\Controllers'], function()
{
	Route::get('/', 'DocsController@index');
});