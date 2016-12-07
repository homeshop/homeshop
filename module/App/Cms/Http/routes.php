<?php
Route::group(['middleware' => 'web', 'prefix' => 'cms', 'namespace' => 'App\Cms\Http\Controllers'], function () {
    Route::get('/', 'CmsController@index');
});