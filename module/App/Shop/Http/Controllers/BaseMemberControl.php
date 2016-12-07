<?php namespace App\Shop\Http\Controllers;
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-30
 * Time: 上午2:05
 */

/********************************** 会员control父类 **********************************************/
class BaseMemberControl extends Control {
    
    protected $member_info = [];   // 会员信息
    
    public function __construct() {
        
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        
        Language::read('common,member_layout');
        
        if($_GET['column'] && strtoupper(CHARSET) == 'GBK'){
            $_GET = Language::getGBK($_GET);
        }
        //会员验证
        $this->checkLogin();
        //输出头部的公用信息
        $this->showLayout();
        Tpl::setDir('member');
        Tpl::setLayout('member_layout');
        
        //获得会员信息
        $this->member_info = $this->getMemberAndGradeInfo(true);
        $this->member_info['voucher_count'] = Model('voucher')->getCurrentAvailableVoucherCount($_SESSION['member_id']);
        $this->member_info['redpacket_count'] = Model('redpacket')->getCurrentAvailableRedpacketCount($_SESSION['member_id']);
        Tpl::output('member_info', $this->member_info);
        
        // 常用操作及导航
        $menu_list = $this->_getNavLink();
        
        //系统公告
        $this->system_notice();
        
        // 交易数量提示
        $this->order_tip();
        
        // 页面高亮
        Tpl::output('act', $_GET['act']);
    }
    
    /**
     * 交易数量提示
     */
    private function order_tip() {
        $model_order = Model('order');
        //交易提醒 - 显示数量
        $order_tip['order_nopay_count'] = $model_order->getOrderCountByID('buyer', $_SESSION['member_id'], 'NewCount');
        $order_tip['order_noreceipt_count'] = $model_order->getOrderCountByID('buyer', $_SESSION['member_id'], 'SendCount');
        $order_tip['order_noeval_count'] = $model_order->getOrderCountByID('buyer', $_SESSION['member_id'], 'EvalCount');
        $order_tip['order_notakes_count'] = $model_order->getOrderCountByID('buyer', $_SESSION['member_id'], 'TakesCount');
        Tpl::output('order_tip', $order_tip);
    }
    
    /**
     * 系统公告
     */
    private function system_notice() {
        $model_message = Model('article');
        $condition = [];
        $condition['ac_id'] = 1;
        $condition['article_position_in'] = ARTICLE_POSIT_ALL.','.ARTICLE_POSIT_BUYER;
        $condition['limit'] = 5;
        $article_list = $model_message->getArticleList($condition);
        Tpl::output('system_notice', $article_list);
    }
    
    /**
     * 常用操作
     * @param string $act
     * 如果菜单中的切换卡不在一个菜单中添加$act参数，值为当前菜单的下标
     */
    protected function _getNavLink($act = '') {
        // 左侧导航
        $menu_list = $this->_getMenuList();
        Tpl::output('menu_list', $menu_list);
    }
    
    /**
     * 左侧导航
     * 菜单数组中child的下标要和其链接的act对应。否则面包屑不能正常显示
     * @return array
     */
    private function _getMenuList() {
        $menu_list = [
            'trade' => [
                'name' => '交易中心',
                'child' => [
                    'member_order' => ['name' => '实物交易订单', 'url' => urlShop('member_order', 'index')],
                    'member_vr_order' => ['name' => '虚拟兑码订单', 'url' => urlShop('member_vr_order', 'index')],
                    'member_evaluate' => ['name' => '交易评价/晒单', 'url' => urlShop('member_evaluate', 'list')],
                    'member_appoint' => ['name' => '预约/到货通知', 'url' => urlShop('member_appoint', 'list')]
                ]
            ],
            'follow' => [
                'name' => '关注中心',
                'child' => [
                    'member_favorite_goods' => [
                        'name' => '商品收藏',
                        'url' => urlShop('member_favorite_goods', 'index')
                    ],
                    'member_favorite_store' => [
                        'name' => '店铺收藏',
                        'url' => urlShop('member_favorite_store', 'index')
                    ],
                    'member_goodsbrowse' => ['name' => '我的足迹', 'url' => urlShop('member_goodsbrowse', 'list')]
                ]
            ],
            'client' => [
                'name' => '客户服务',
                'child' => [
                    'member_refund' => [
                        'name' => '退款及退货',
                        'url' => urlShop('member_refund', 'index')
                    ],
                    'member_complain' => ['name' => '交易投诉', 'url' => urlShop('member_complain', 'index')],
                    'member_consult' => ['name' => '商品咨询', 'url' => urlShop('member_consult', 'my_consult')],
                    'member_inform' => ['name' => '违规举报', 'url' => urlShop('member_inform', 'index')],
                    'member_mallconsult' => ['name' => '平台客服', 'url' => urlShop('member_mallconsult', 'index')]
                ]
            ],
            'info' => [
                'name' => '会员资料',
                'child' => [
                    'member_information' => [
                        'name' => '账户信息',
                        'url' => urlMember('member_information', 'member')
                    ],
                    'member_address' => ['name' => '收货地址', 'url' => urlMember('member_address', 'address')]
                ]
            ],
            'property' => [
                'name' => '财产中心',
                'child' => [
                    'predeposit' => [
                        'name' => '账户余额',
                        'url' => urlMember('predeposit', 'pd_log_list')
                    ],
                    'member_voucher' => ['name' => '我的代金券', 'url' => urlMember('member_voucher', 'index')],
                    'member_redpacket' => ['name' => '我的红包', 'url' => urlMember('member_redpacket', 'index')]
                ]
            ],
        ];
        return $menu_list;
    }
}