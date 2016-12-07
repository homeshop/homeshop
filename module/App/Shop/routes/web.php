<?php namespace App\Shop\Http\Controllers;

use Route;
use App;

Route::group(['middleware' => 'web', 'prefix' => 'shop', 'namespace' => __NAMESPACE__],
    function () {
        Route::any('{ctl?}', function ($ctl = '') {
            return App::make(Controller::class)->route($ctl);
        })->where(['ctl' => '.*']);
    });