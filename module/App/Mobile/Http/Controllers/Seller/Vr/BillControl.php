<?php namespace App\Mobile\Http\Controllers\Seller\Vr;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 虚拟订单结算
 */
class BillControl extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 结算列表
     */
    public function listOp() {
        $model_bill = Model('vr_bill');
        $condition = [];
        $condition['ob_store_id'] = $this->store_info['store_id'];
        if(preg_match('/^\d+$/', $_POST['ob_id'])){
            $condition['ob_id'] = intval($_POST['ob_id']);
        }
        if(is_numeric($_POST['bill_state'])){
            $condition['ob_state'] = intval($_POST['bill_state']);
        }
        $bill_list = $model_bill->getOrderBillList($condition, '*', $this->page, 'ob_state asc,ob_id asc');
        $page_count = $model_bill->gettotalpage();
        output_data(['bill_list' => $bill_list], mobile_page($page_count));
    }
}
