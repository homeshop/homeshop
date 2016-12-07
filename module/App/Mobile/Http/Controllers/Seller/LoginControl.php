<?php namespace App\Mobile\Http\Controllers\Seller;

use App\Mobile\Http\Controllers\MobileHomeControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家登录
 */
class LoginControl extends MobileHomeControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 登录
     */
    public function indexOp() {
        if(empty($_POST['seller_name']) || empty($_POST['password']) || !in_array($_POST['client'], $this->client_type_array)){
            output_error('用户名密码不能为空');
        }
        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(['seller_name' => $_POST['seller_name']]);
        if(!$seller_info){
            output_error('登录失败');
        }
        //店铺所有人或者授权的子账号可以从客户端登录
        if(!($seller_info['is_admin'] || $seller_info['is_client'])){
            output_error('权限验证失败');
        }
        //验证身份
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfo([
            'member_id' => $seller_info['member_id'],
            'member_passwd' => strtolower($_POST['password'])
        ]);
        if(!$member_info){
            output_error('用户名密码错误');
        }
        //读取店铺信息
        $model_store = Model('store');
        $store_info = $model_store->getStoreInfoByID($seller_info['store_id']);
        //更新卖家登陆时间
        $model_seller->editSeller(['last_login_time' => TIMESTAMP], ['seller_id' => $seller_info['seller_id']]);
        //生成登录令牌
        $token = $this->_get_token($seller_info['seller_id'], $seller_info['seller_name'], $_POST['client']);
        if($token){
            output_data([
                'seller_name' => $seller_info['seller_name'],
                'store_name' => $store_info['store_name'],
                'key' => $token
            ]);
        } else {
            output_error('登录失败');
        }
    }
    
    /**
     * 登录生成token
     */
    private function _get_token($seller_id, $seller_name, $client) {
        $model_mb_seller_token = Model('mb_seller_token');
        //重新登录后以前的令牌失效
        $condition = [];
        $condition['seller_id'] = $seller_id;
        $model_mb_seller_token->delSellerToken($condition);
        //生成新的token
        $mb_seller_token_info = [];
        $token = md5($seller_name . strval(TIMESTAMP) . strval(rand(0, 999999)));
        $mb_seller_token_info['seller_id'] = $seller_id;
        $mb_seller_token_info['seller_name'] = $seller_name;
        $mb_seller_token_info['token'] = $token;
        $mb_seller_token_info['login_time'] = TIMESTAMP;
        $mb_seller_token_info['client_type'] = $client;
        $result = $model_mb_seller_token->addSellerToken($mb_seller_token_info);
        if($result){
            return $token;
        } else {
            return null;
        }
    }
}
