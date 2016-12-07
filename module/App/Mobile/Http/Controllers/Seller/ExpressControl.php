<?php namespace App\Mobile\Http\Controllers\Seller;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家注销
 */
class Control extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 物流列表
     */
    public function get_listOp() {
        $express_list = rkcache('express', true);
        $express_array = [];
        //快递公司
        $my_express_list = Model()->table('store_extend')->getfby_store_id($this->store_info['store_id'], 'express');
        if(!empty($my_express_list)){
            $my_express_list = explode(',', $my_express_list);
            foreach($my_express_list as $val){
                $express_array[ $val ] = $express_list[ $val ];
            }
        }
        output_data(['express_array' => $express_array]);
    }
    
    /**
     * 自提物流列表
     */
    public function get_zt_listOp() {
        $express_list = rkcache('express', true);
        foreach($express_list as $k => $v){
            if($v['e_zt_state'] == '0'){
                unset($express_list[ $k ]);
            }
        }
        output_data(['express_array' => $express_list]);
    }
}
