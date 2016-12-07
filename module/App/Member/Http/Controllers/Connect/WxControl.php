<?php namespace App\Member\Http\Controllers\Connect;

use App\Member\Http\Controllers\BaseLoginControl;
use App\Member\Http\Controllers\Language;
use App\Member\Http\Controllers\Tpl;

/**
 * 微信登录
 */
class  WxControl extends BaseLoginControl {
    
    public function __construct() {
        parent::__construct();
        Language::read("home_login_register,home_login_index");
        Tpl::output('hidden_login', 1);
    }
    
    /**
     * 微信登录
     */
    public function indexOp() {
        if(empty($_GET['code'])){
            Tpl::showpage('connect_wx.index', 'null_layout');
        } else {
            $this->get_infoOp();
        }
    }
    
    /**
     * 微信注册后修改密码
     */
    public function edit_infoOp() {
        if(chksubmit()){
            $model_member = Model('member');
            $member = [];
            $member['member_passwd'] = md5($_POST["password"]);
            if(!empty($_POST["email"])){
                $member['member_email'] = $_POST["email"];
                $_SESSION['member_email'] = $_POST["email"];
            }
            $model_member->editMember(['member_id' => $_SESSION['member_id']], $member);
            showDialog(Language::get('nc_common_save_succ'), urlMember('member', 'home'), 'succ');
        }
    }
    
    /**
     * 回调获取信息
     */
    public function get_infoOp() {
        $code = $_GET['code'];
        if(!empty($code)){
            $logic_connect_api = Logic('connect_api');
            $user_info = $logic_connect_api->getWxUserInfo($code);
            if(!empty($user_info['unionid'])){
                $unionid = $user_info['unionid'];
                $model_member = Model('member');
                $member = $model_member->getMemberInfo(['weixin_unionid' => $unionid]);
                if(!empty($member)){//会员信息存在时自动登录
                    $model_member->createSession($member);
                    showDialog('登录成功', urlMember('member', 'home'), 'succ');
                }
                if(!empty($_SESSION['member_id'])){//已登录时绑定微信
                    $member_id = $_SESSION['member_id'];
                    $member = [];
                    $member['weixin_unionid'] = $unionid;
                    $member['weixin_info'] = $user_info['weixin_info'];
                    $model_member->editMember(['member_id' => $member_id], $member);
                    showDialog('微信绑定成功', urlMember('member', 'home'), 'succ');
                } else {//自动注册会员并登录
                    $this->register($user_info);
                    exit;
                }
            }
        }
        showDialog('微信登录失败', urlLogin('login', 'index'), 'succ');
    }
    
    /**
     * 注册
     */
    public function register($user_info) {
        Language::read("home_login_register,home_login_index");
        $unionid = $user_info['unionid'];
        $nickname = $user_info['nickname'];
        if(!empty($unionid)){
            $logic_connect_api = Logic('connect_api');
            $member = $logic_connect_api->wxRegister($user_info, 'www');
            if(!empty($member)){
                $model_member = Model('member');
                $model_member->createSession($member, true);//自动登录
                Tpl::output('user_info', $user_info);
                Tpl::output('headimgurl', $user_info['headimgurl']);
                Tpl::output('password', $member['password']);
                Tpl::showpage('connect_wx.register');
            }
        }
    }
}