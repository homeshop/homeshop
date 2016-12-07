<?php namespace App\Cms\Http\Controllers;

/**
 * cms首页
 */
class  IndexControl extends CMSHomeControl {
    
    public function __construct() {
        parent::__construct();
        Tpl::output('index_sign', 'index');
    }
    
    public function indexOp() {
        Tpl::showpage('index');
    }
}
