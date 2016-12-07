<?php namespace App\Shop\Http\Controllers;



/**
 * 会员中心——账户概览
 */
class  MemberControl extends BaseMemberControl {
    
    /**
     * 我的商城
     */
    public function homeOp() {
        Tpl::showpage('member_home');
    }
    
    public function ajax_load_member_infoOp() {
        
        $member_info = $this->member_info;
        $member_info['security_level'] = Model('member')->getMemberSecurityLevel($member_info);
        
        //代金券数量
        $member_info['voucher_count'] = Model('voucher')->getCurrentAvailableVoucherCount($_SESSION['member_id']);
        Tpl::output('home_member_info', $member_info);
        
        Tpl::showpage('member_home.member_info', 'null_layout');
    }
    
    public function ajax_load_order_infoOp() {
        $model_order = Model('order');
        
        //交易提醒 - 显示数量
        $member_info['order_nopay_count'] = $model_order->getOrderCountByID('buyer', $_SESSION['member_id'], 'NewCount');
        $member_info['order_noreceipt_count'] = $model_order->getOrderCountByID('buyer', $_SESSION['member_id'], 'SendCount');
        $member_info['order_noeval_count'] = $model_order->getOrderCountByID('buyer', $_SESSION['member_id'], 'EvalCount');
        Tpl::output('home_member_info', $member_info);
        
        //交易提醒 - 显示订单列表
        $condition = [];
        $condition['buyer_id'] = $_SESSION['member_id'];
        $condition['order_state'] = [
            'in',
            [ORDER_STATE_NEW, ORDER_STATE_PAY, ORDER_STATE_SEND, ORDER_STATE_SUCCESS]
        ];
        $order_list = $model_order->getNormalOrderList($condition, '', '*', 'order_id desc', 3, ['order_goods']);
        
        foreach($order_list as $order_id => $order){
            //显示物流跟踪
            $order_list[$order_id]['if_deliver'] = $model_order->getOrderOperateState('deliver', $order);
            //显示评价
            $order_list[$order_id]['if_evaluation'] = $model_order->getOrderOperateState('evaluation', $order);
            //显示支付
            $order_list[$order_id]['if_payment'] = $model_order->getOrderOperateState('payment', $order);
            //显示收货
            $order_list[$order_id]['if_receive'] = $model_order->getOrderOperateState('receive', $order);
        }
        
        Tpl::output('order_list', $order_list);
        
        //取出购物车信息
        $model_cart = Model('cart');
        $cart_list = $model_cart->listCart('db', ['buyer_id' => $_SESSION['member_id']], 3);
        Tpl::output('cart_list', $cart_list);
        Tpl::showpage('member_home.order_info', 'null_layout');
    }
    
    public function ajax_load_goods_infoOp() {
        //商品收藏
        $favorites_model = Model('favorites');
        $favorites_list = $favorites_model->getGoodsFavoritesList(['member_id' => $_SESSION['member_id']], '*', 7);
        if(!empty($favorites_list) && is_array($favorites_list)){
            $favorites_id = [];//收藏的商品编号
            foreach($favorites_list as $key => $fav){
                $favorites_id[] = $fav['fav_id'];
            }
            $goods_model = Model('goods');
            $field = 'goods_id,goods_name,store_id,goods_image,goods_promotion_price';
            $goods_list = $goods_model->getGoodsList(['goods_id' => ['in', $favorites_id]], $field);
            Tpl::output('favorites_list', $goods_list);
        }
        
        //店铺收藏
        $favorites_list = $favorites_model->getStoreFavoritesList(['member_id' => $_SESSION['member_id']], '*', 6);
        if(!empty($favorites_list) && is_array($favorites_list)){
            $favorites_id = [];//收藏的店铺编号
            foreach($favorites_list as $key => $fav){
                $favorites_id[] = $fav['fav_id'];
            }
            $store_model = Model('store');
            $store_list = $store_model->getStoreList(['store_id' => ['in', $favorites_id]]);
            Tpl::output('favorites_store_list', $store_list);
        }
        
        $goods_count_new = [];
        if(!empty($favorites_id)){
            foreach($favorites_id as $v){
                $count = Model('goods')->getGoodsCommonOnlineCount(['store_id' => $v]);
                $goods_count_new[$v] = $count;
            }
        }
        Tpl::output('goods_count', $goods_count_new);
        Tpl::showpage('member_home.goods_info', 'null_layout');
    }
    
    public function ajax_load_sns_infoOp() {
        //我的足迹
        $goods_list = Model('goods_browse')->getViewedGoodsList($_SESSION['member_id'], 20);
        $viewed_goods = [];
        if(is_array($goods_list) && !empty($goods_list)){
            foreach($goods_list as $key => $val){
                $goods_id = $val['goods_id'];
                $val['url'] = urlShop('goods', 'index', ['goods_id' => $goods_id]);
                $val['goods_image'] = thumb($val, 240);
                $viewed_goods[$goods_id] = $val;
            }
        }
        Tpl::output('viewed_goods', $viewed_goods);
        
        //我的圈子
        $model = Model();
        $circlemember_array = $model->table('circle_member')->where(['member_id' => $_SESSION['member_id']])->select();
        if(!empty($circlemember_array)){
            $circlemember_array = array_under_reset($circlemember_array, 'circle_id');
            $circleid_array = array_keys($circlemember_array);
            $circle_list = $model->table('circle')->where([
                'circle_id' => [
                    'in',
                    $circleid_array
                ]
            ])->limit(6)->select();
            Tpl::output('circle_list', $circle_list);
        }
        
        //好友动态
        $model_fd = Model('sns_friend');
        $fields = 'member.member_id,member.member_name,member.member_avatar';
        $follow_list = $model_fd->listFriend([
            'limit' => 15,
            'friend_frommid' => "{$_SESSION['member_id']}"
        ], $fields, '', 'detail');
        $member_ids = [];
        $follow_list_new = [];
        if(is_array($follow_list)){
            foreach($follow_list as $v){
                $follow_list_new[$v['member_id']] = $v;
                $member_ids[] = $v['member_id'];
            }
        }
        $tracelog_model = Model('sns_tracelog');
        //条件
        $condition = [];
        $condition['trace_memberid'] = ['in', $member_ids];
        $condition['trace_privacy'] = 0;
        $condition['trace_state'] = 0;
        $tracelist = Model()->table('sns_tracelog')->where($condition)->field('count(*) as ccount,trace_memberid')->group('trace_memberid')->limit(5)->select();
        $tracelist_new = [];
        $follow_list = [];
        if(!empty($tracelist)){
            foreach($tracelist as $k => $v){
                $tracelist_new[$v['trace_memberid']] = $v['ccount'];
                $follow_list[] = $follow_list_new[$v['trace_memberid']];
            }
        }
        Tpl::output('tracelist', $tracelist_new);
        Tpl::output('follow_list', $follow_list);
        Tpl::showpage('member_home.sns_info', 'null_layout');
    }
}
