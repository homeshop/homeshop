<?php

Route::group(['middleware' => 'web', 'prefix' => 'crontab', 'namespace' => 'App\Crontab\Http\Controllers'], function()
{
	Route::get('/', 'CrontabController@index');
});