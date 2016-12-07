<?php namespace App\Member\Http\Controllers;

class  BaseMemberControl extends Control {
    
    protected $member_info = [];   // 会员信息
    
    public function __construct() {
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        Language::read('common,member_layout');
        //会员验证
        $this->checkLogin();
        //输出头部的公用信息
        $this->showLayout();
        Tpl::setLayout('member_layout');
        //获得会员信息
        $this->member_info = $this->getMemberAndGradeInfo(true);
        Tpl::output('member_info', $this->member_info);
        // 左侧导航
        $menu_list = $this->_getMenuList();
        Tpl::output('menu_list', $menu_list);
        // 系统消息
        $this->system_notice();
        // 页面高亮
        Tpl::output('act', $_GET['act']);
        /**
         * 文章
         */
        $this->article();
    }
    
    /**
     * 左侧导航
     * 菜单数组中child的下标要和其链接的act对应。否则面包屑不能正常显示
     * @return array
     */
    private function _getMenuList() {
        $menu_list = [
            'info' => [
                'name' => '会员资料',
                'child' => [
                    'member_information' => ['name' => '账户信息', 'url' => urlMember('member_information', 'member')],
                    'member_security' => ['name' => '账户安全', 'url' => urlMember('member_security', 'index')],
                    'member_address' => ['name' => '收货地址', 'url' => urlMember('member_address', 'address')],
                    'member_message' => ['name' => '我的消息', 'url' => urlMember('member_message', 'message')],
                    'member_snsfriend' => ['name' => '我的好友', 'url' => urlMember('member_snsfriend', 'find')],
                    'member_bind' => ['name' => '第三方账号登录', 'url' => urlMember('member_bind', 'qqbind')],
                    'member_sharemanage' => ['name' => '分享绑定', 'url' => urlMember('member_sharemanage', 'index')]
                ]
            ],
            'property' => [
                'name' => '财产中心',
                'child' => [
                    'consume' => ['name' => '消费记录', 'url' => urlMember('consume')],
                    'predeposit' => ['name' => '账户余额', 'url' => urlMember('predeposit', 'pd_log_list')],
                    'member_points' => ['name' => '我的积分', 'url' => urlMember('member_points', 'index')],
                    'member_voucher' => ['name' => '我的代金券', 'url' => urlMember('member_voucher', 'index')],
                    'member_redpacket' => ['name' => '我的红包', 'url' => urlMember('member_redpacket', 'index')]
                ]
            ]
        ];
        return $menu_list;
    }
}