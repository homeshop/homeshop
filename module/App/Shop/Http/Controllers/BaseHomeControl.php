<?php namespace App\Shop\Http\Controllers;
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-30
 * Time: 上午2:04
 */

/********************************** 前台control父类 **********************************************/
class BaseHomeControl extends Control {
    
    public function __construct() {
        //输出头部的公用信息
        $this->showLayout();
        //输出会员信息
        $this->getMemberAndGradeInfo(false);
        
        Language::read('common,home_layout');
        
        Tpl::setDir('home');
        
        Tpl::setLayout('home_layout');
        
        if($_GET['column'] && strtoupper(CHARSET) == 'GBK'){
            $_GET = Language::getGBK($_GET);
        }
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        // 自动登录
        $this->auto_login();
    }
    
}