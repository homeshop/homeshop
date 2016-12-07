<?php

Route::group(['middleware' => 'web', 'prefix' => 'member', 'namespace' => 'App\Member\Http\Controllers'], function()
{
	Route::get('/', 'MemberController@index');
});