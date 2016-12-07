<?php namespace App\Shop\Http\Controllers;
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-30
 * Time: 上午2:15
 */

class BaseChainControl extends BaseStoreControl {
    
    public function __construct() {
        
        Language::read('common,store_layout');
        
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        
        Tpl::setDir('store');
        Tpl::setLayout('home_layout');
        
        //输出头部的公用信息
        $this->showLayout();
    }
    
}