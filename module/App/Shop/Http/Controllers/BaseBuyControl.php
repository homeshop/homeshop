<?php namespace App\Shop\Http\Controllers;
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-30
 * Time: 上午2:04
 */

/********************************** 购买流程父类 **********************************************/
class BaseBuyControl extends Control {
    
    protected $member_info = [];   // 会员信息
    
    protected function __construct() {
        Language::read('common,home_layout');
        //输出会员信息
        $this->member_info = $this->getMemberAndGradeInfo(true);
        Tpl::output('member_info', $this->member_info);
        
        Tpl::setDir('buy');
        Tpl::setLayout('buy_layout');
        if($_GET['column'] && strtoupper(CHARSET) == 'GBK'){
            $_GET = Language::getGBK($_GET);
        }
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        //获取导航
        Tpl::output('nav_list', rkcache('nav', true));
        
        Tpl::output('contract_list', Model('contract')->getContractItemByCache());
    }
}