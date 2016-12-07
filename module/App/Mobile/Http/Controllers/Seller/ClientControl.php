<?php namespace App\Mobile\Http\Controllers\Seller;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家注销
 */
class ClientControl extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 客户列表
     */
    public function listOp() {
        $model_stat = Model('stat');
        $member_list = [];
        $condition = [];
        $condition['store_id'] = $this->store_info['store_id'];
        if(!empty($_POST['member_name'])){
            $condition['buyer_name'] = ['like', '%' . $_POST['member_name'] . '%'];
        }
        $count = $model_stat->getStatOrderCount($condition, 'distinct buyer_id');
        $list = $model_stat->statByStatorder($condition, 'buyer_id', [$this->page, $count], 0, '', 'buyer_id');
        if(!empty($list)){
            $memberid_array = [];
            foreach($list as $val){
                $memberid_array[] = $val['buyer_id'];
            }
            $member_list = Model('member')->getMemberList([
                'member_id' => [
                    'in',
                    $memberid_array
                ]
            ], 'member_id,member_name,member_email,member_mobile');
        }
        if(!empty($member_list)){
            foreach($member_list as $key => $val){
                $member_list[ $key ]['member_avatar'] = getMemberAvatarForID($val['member_id']);
            }
        }
        $page_count = $model_stat->gettotalpage();
        output_data(['member_list' => $member_list], mobile_page($page_count));
    }
    
    /**
     * 客户购买商品列表
     */
    public function goods_listOp() {
        $model_stat = Model('stat');
        $goods_list = [];
        $condition['store_id'] = $this->store_info['store_id'];
        if(is_numeric($_POST['member_id'])){
            $condition['buyer_id'] = intval($_POST['member_id']);
        }
        $goods_list = $model_stat->statByStatordergoods($condition, 'store_id,goods_name,goods_image,goods_num,goods_price,order_add_time', $this->page);
        if(!empty($goods_list)){
            foreach($goods_list as $key => $val){
                $goods_list[ $key ]['gooods_image'] = thumb($val, '240');
            }
        }
        $page_count = $model_stat->gettotalpage();
        output_data(['goods_list' => $goods_list], mobile_page($page_count));
    }
}
