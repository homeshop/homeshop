<?php

if(is_web() && !is_file(storage_path('install.lock')) && is_file(public_path("install/index.php"))){
    header("location: ".env('APP_URL')."/install/index.php") or exit;
}

Config::get('constants');
$config = Config::get('global');

if(!is_cli()){
    
    // 解析 Rewrite 后的 URL 到 $_GET
    RouteUtil::ParseRewriteUrl();
    
    //启用ZIP压缩
    if($config['gzip'] == 1 && function_exists('ob_gzhandler') && $_GET['inajax'] != 1){
        ob_start('ob_gzhandler');
    } else {
        ob_start();
    }
}

spl_autoload_register(['Base', 'autoload']);

require __DIR__.'/Http/routes.php';
