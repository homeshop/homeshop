<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 会员评价
 */
class EvaluateControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 评论
     */
    public function indexOp() {
        $order_id = intval($_GET['order_id']);
        $return = Logic('member_evaluate')->validation($order_id, $this->member_info['member_id']);
        if(!$return['state']){
            output_error($return['msg']);
        }
        extract($return['data']);
        $store = [];
        $store['store_id'] = $store_info['store_id'];
        $store['store_name'] = $store_info['store_name'];
        $store['is_own_shop'] = $store_info['is_own_shop'];
        output_data(['store_info' => $store, 'order_goods' => $order_goods]);
    }
    
    /**
     * 评论保存
     */
    public function saveOp() {
        $order_id = intval($_POST['order_id']);
        $return = Logic('member_evaluate')->validation($order_id, $this->member_info['member_id']);
        if(!$return['state']){
            output_error($return['msg']);
        }
        extract($return['data']);
        $return = Logic('member_evaluate')->save($_POST, $order_info, $store_info, $order_goods, $this->member_info['member_id'], $this->member_info['member_name']);
        if(!$return['state']){
            output_data($return['msg']);
        } else {
            output_data('1');
        }
    }
    
    /**
     * 追评
     */
    public function againOp() {
        $order_id = intval($_GET['order_id']);
        $return = Logic('member_evaluate')->validationAgain($order_id, $this->member_info['member_id']);
        if(!$return['state']){
            output_error($return['msg']);
        }
        extract($return['data']);
        $store = [];
        $store['store_id'] = $store_info['store_id'];
        $store['store_name'] = $store_info['store_name'];
        $store['is_own_shop'] = $store_info['is_own_shop'];
        output_data(['store_info' => $store, 'evaluate_goods' => $evaluate_goods]);
    }
    
    /**
     * 追加评价保存
     */
    public function save_againOp() {
        $order_id = intval($_POST['order_id']);
        $return = Logic('member_evaluate')->validationAgain($order_id, $this->member_info['member_id']);
        if(!$return['state']){
            output_error($return['msg']);
        }
        extract($return['data']);
        $return = Logic('member_evaluate')->saveAgain($_POST, $order_info, $evaluate_goods);
        if(!$return['state']){
            output_data($return['msg']);
        } else {
            output_data('1');
        }
    }
    
    /**
     * 虚拟订单评价
     */
    public function vrOp() {
        $order_id = intval($_GET['order_id']);
        $return = Logic('member_evaluate')->validationVr($order_id, $this->member_info['member_id']);
        if(!$return['state']){
            output_error($return['msg']);
        }
        extract($return['data']);
        output_data(['order_info' => $order_info]);
    }
    
    /**
     * 虚拟订单评价保存
     */
    public function save_vrOp() {
        $order_id = intval($_POST['order_id']);
        $return = Logic('member_evaluate')->validationVr($order_id, $this->member_info['member_id']);
        if(!$return['state']){
            output_error($return['msg']);
        }
        extract($return['data']);
        $return = Logic('member_evaluate')->saveVr($_POST, $order_info, $store_info, $this->member_info['member_id'], $this->member_info['member_name']);
        if(!$return['state']){
            output_data($return['msg']);
        } else {
            output_data('1');
        }
    }
}
