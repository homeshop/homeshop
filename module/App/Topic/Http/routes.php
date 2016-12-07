<?php

Route::group(['middleware' => 'web', 'prefix' => 'topic', 'namespace' => 'App\Topic\Http\Controllers'], function()
{
	Route::get('/', 'TopicController@index');
});