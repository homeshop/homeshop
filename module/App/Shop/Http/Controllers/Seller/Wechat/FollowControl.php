<?php namespace App\Shop\Http\Controllers\Seller\Wechat;

use App\Shop\Http\Controllers\BaseSellerControl;
use App\Shop\Http\Controllers\Language;
use App\Shop\Http\Controllers\Tpl;


/**
 * 微信关注自动回复管理
 */
class  FollowControl extends BaseSellerControl {
    
    /**
     * 构造方法
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 关注自动回复
     */
    public function follow_indexOp() {
        $account_id = intval($_SESSION['member_id']);
        $model_wechat = Model('wechat');
        $find_data = $model_wechat->where(['user_id' => $account_id])->find();
        if(empty($find_data)){
            @header('Location: index.php?act=seller_wechat&op=index');
            //app_redirect(url("biz","wechat"));return ;
            //showErr('请先设置微信接口！','马上去设置','index.php?app=wechat');return ;
        }
        
        /*$userid=$this->visitor->get('user_id');
        $model_wechat=& m('wechat');
        $find_data = $model_wechat->get("user_id =".$userid);
        if(empty($find_data))
        {
        $this->show_warning('请先设置微信接口！','马上去设置','index.php?app=wechat');return ;
        }
        
        
        $this->_curlocal('微信管理',url('app=wechat&act=follow_index'),'关注自动回复');*/
        /* 当前用户中心菜单 */
        //$this->_curitem('follow_index');
        //$this->_config_seo('title', '关注自动回复');
        $this->profile_menu('wechat_follow');
        Tpl::showpage('seller_wechat_follow_index');
    }
    
    /**
     * 用户中心右边，小导航
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = [];
        $menu_array[] = [
            'menu_key' => 'wechat_follow',
            'menu_name' => '关注自动回复设置',
            'menu_url' => urlShop('seller_wechat_follow', 'follow_index')
        ];
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }
}
