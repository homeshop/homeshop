<?php

/**
 * 商家 预约/到货通知
 */
class store_appointControl extends BaseSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 买家我的订单，以总订单pay_sn来分组显示
     */
    public function indexOp() {
        $model_arrtivalnotice = Model('arrival_notice');
        $appoint_list = $model_arrtivalnotice->getArrivalNoticeList(['store_id' => $_SESSION['store_id']], '*', '', '15');
        if(!empty($appoint_list)){
            $memberid_array = [];
            foreach($appoint_list as $val){
                $memberid_array[] = $val['member_id'];
            }
            $member_list = Model('member')->getMemberList([
                'member_id' => [
                    'in',
                    $memberid_array
                ]
            ], 'member_id,member_name');
            $member_list = array_under_reset($member_list, 'member_id');
            Tpl::output('member_list', $member_list);
        }
        Tpl::output('appoint_list', $appoint_list);
        Tpl::output('show_page', $model_arrtivalnotice->showpage());
        self::profile_menu('member_appoint');
        Tpl::showpage('store_appoint.list');
    }
    
    /**
     * 删除
     */
    public function del_appointOp() {
        $id = intval($_GET['id']);
        $model_arrtivalnotice = Model('arrival_notice');
        $model_arrtivalnotice->delArrivalNotice(['store_id' => $_SESSION['store_id'], 'an_id' => $id]);
        showDialog('操作成功', 'reload', 'succ');
    }
    
    /**
     * 用户中心右边，小导航
     * @param string $menu_type 导航类型
     * @param string $menu_key  当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = [
            [
                'menu_key' => 'member_appoint',
                'menu_name' => '预约/到货通知列表',
                'menu_url' => 'index.php?act=store_appoint'
            ]
        ];
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }
}
