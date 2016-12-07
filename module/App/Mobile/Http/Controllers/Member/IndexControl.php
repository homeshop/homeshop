<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 我的商城
 */
class IndexControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 我的商城
     */
    public function indexOp() {
        $member_info = [];
        $member_info['user_name'] = $this->member_info['member_name'];
        $member_info['avatar'] = getMemberAvatarForID($this->member_info['member_id']);
        $member_gradeinfo = Model('member')->getOneMemberGrade(intval($this->member_info['member_exppoints']));
        $member_info['level_name'] = $member_gradeinfo['level_name'];
        $member_info['favorites_store'] = Model('favorites')->getStoreFavoritesCountByMemberId($this->member_info['member_id']);
        $member_info['favorites_goods'] = Model('favorites')->getGoodsFavoritesCountByMemberId($this->member_info['member_id']);
        // 交易提醒
        $model_order = Model('order');
        $member_info['order_nopay_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'NewCount');
        $member_info['order_noreceipt_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'SendCount');
        $member_info['order_notakes_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'TakesCount');
        $member_info['order_noeval_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'EvalCount');
        // 售前退款
        $condition = [];
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['refund_state'] = ['lt', 3];
        $member_info['return'] = Model('refund_return')->getRefundReturnCount($condition);
        output_data(['member_info' => $member_info]);
    }
    
    /**
     * 我的资产
     */
    public function my_assetOp() {
        $param = $_GET;
        $fields_arr = ['point', 'predepoit', 'available_rc_balance', 'redpacket', 'voucher'];
        $fields_str = trim($param['fields']);
        if($fields_str){
            $fields_arr = explode(',', $fields_str);
        }
        $member_info = [];
        if(in_array('point', $fields_arr)){
            $member_info['point'] = $this->member_info['member_points'];
        }
        if(in_array('predepoit', $fields_arr)){
            $member_info['predepoit'] = $this->member_info['available_predeposit'];
        }
        if(in_array('available_rc_balance', $fields_arr)){
            $member_info['available_rc_balance'] = $this->member_info['available_rc_balance'];
        }
        if(in_array('redpacket', $fields_arr)){
            $member_info['redpacket'] = Model('redpacket')->getCurrentAvailableRedpacketCount($this->member_info['member_id']);
        }
        if(in_array('voucher', $fields_arr)){
            $member_info['voucher'] = Model('voucher')->getCurrentAvailableVoucherCount($this->member_info['member_id']);
        }
        output_data($member_info);
    }
}
