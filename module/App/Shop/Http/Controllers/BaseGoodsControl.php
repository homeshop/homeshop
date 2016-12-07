<?php namespace App\Shop\Http\Controllers;
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-30
 * Time: 上午2:07
 */

class BaseGoodsControl extends BaseStoreControl {
    
    public function __construct() {
        
        Language::read('common,store_layout');
        
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        
        Tpl::setDir('store');
        Tpl::setLayout('home_layout');
        
        //输出头部的公用信息
        $this->showLayout();
        //输出会员信息
        $this->getMemberAndGradeInfo(false);
    }
    
    protected function getStoreInfo($store_id, $goods_info = null) {
        $model_store = Model('store');
        $store_info = $model_store->getStoreOnlineInfoByID($store_id);
        if(empty($store_info)){
            showMessage(L('nc_store_close'), '', '', 'error');
        }
        if($_COOKIE['dregion']){
            $store_info['deliver_region'] = $_COOKIE['dregion'];
        }
        if(strpos($store_info['deliver_region'], '|')){
            $store_info['deliver_region'] = explode('|', $store_info['deliver_region']);
            $store_info['deliver_region_ids'] = explode(' ', $store_info['deliver_region'][0]);
            $store_info['deliver_region_names'] = explode(' ', $store_info['deliver_region'][1]);
        }
        $this->outputStoreInfo($store_info, $goods_info);
    }
}