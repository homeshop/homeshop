<?php namespace App\Microshop\Http\Controllers;

/**
 * 默认展示页面
 */
class  AlbumControl extends MircroShopControl {
    
    public function __construct() {
        parent::__construct();
        Tpl::output('index_sign', 'album');
    }
    
    //首页
    public function indexOp() {
        Tpl::showpage('album');
    }
}
