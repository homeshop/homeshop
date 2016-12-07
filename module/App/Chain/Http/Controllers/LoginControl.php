<?php namespace App\Chain\Http\Controllers;

/**
 * 物流自提服务站首页
 */
class  LoginControl extends BaseAccountCenterControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 登录
     */
    public function indexOp() {
        if($_SESSION['chain_login'] == 1){
            @header('location: index.php?act=index');
            die;
        }
        if(chksubmit(true, true)){
            $where = [];
            $where['chain_user'] = $_POST['user'];
            $where['chain_pwd'] = md5($_POST['pwd']);
            $chain_info = Model('chain')->getChainInfo($where);
            if(!empty($chain_info)){
                $_SESSION['chain_login'] = 1;
                $_SESSION['chain_id'] = $chain_info['chain_id'];
                $_SESSION['chain_store_id'] = $chain_info['store_id'];
                $_SESSION['chain_user'] = $chain_info['chain_user'];
                $_SESSION['chain_name'] = $chain_info['chain_name'];
                $_SESSION['chain_img'] = getChainImage($chain_info['chain_img'], $chain_info['store_id']);
                $_SESSION['chain_address'] = $chain_info['area_info'] . ' ' . $chain_info['chain_address'];
                $_SESSION['chain_phone'] = $chain_info['chain_phone'];
                showDialog('登录成功', 'index.php?act=index', 'succ');
            } else {
                showDialog('登录失败');
            }
        }
        Tpl::showpage('login');
    }
    
    /**
     * 登出
     */
    public function logoutOp() {
        unset($_SESSION['chain_login']);
        unset($_SESSION['chain_id']);
        unset($_SESSION['chain_store_id']);
        unset($_SESSION['chain_user']);
        unset($_SESSION['chain_name']);
        unset($_SESSION['chain_img']);
        unset($_SESSION['chain_address']);
        unset($_SESSION['chain_phone']);
        showDialog('退出成功', 'reload', 'succ');
    }
}
