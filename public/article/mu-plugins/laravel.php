<?php
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-26
 * Time: 上午4:07
 */


if (!defined('IN_HOMESHOP')) {
    define('NOT_LOAD_WP', true);
    require_once ABSPATH . '/../../core/common.php';
}


//
//add_filter('theme_root', function($in){
//    $new_root = dirname(ABSPATH) . '/themes';
//    return $new_root;
//});

require_once ABSPATH . '/../../core/framework/function/core.php';
require ABSPATH . '/../../../bootstrap/autoload.php';


call_user_func(function () {

    /** @var App\Core\Application $app */
    $app = require_once ABSPATH . '/../../../bootstrap/app.php';

    /** @var App\Core\Http\Kernel $kernel */
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    if (is_object($response)) {
        $response->send();
    }

    $kernel->terminate($request, $response);
});


//add_action('init', function () {
add_action('', function () {
    if (is_404()) {
        status_header(200);
    }
});

//add_filter('theme_root', function ($dir) {
//    kd($dir);
//    register_theme_directory($path = dirname(ABSPATH) . '/theme');
//    return $path;
//});
//
//add_filter('theme_root_uri', function ($in) {
//    return 'http://' . $_SERVER['HTTP_HOST'] . '/theme';
//});

//});

