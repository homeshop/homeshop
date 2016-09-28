<?php

if (is_dir(__DIR__ . '/topic') && empty($_POST) && trim($_SERVER['REQUEST_URI'], '/') == '') {
    require 'topic/index.php';
} else {
    $site_url = strtolower('http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/index.php')) . '/shop/index.php');
    include('shop/index.php');
}


