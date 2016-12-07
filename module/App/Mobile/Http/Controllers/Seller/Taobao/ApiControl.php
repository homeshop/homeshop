<?php namespace App\Mobile\Http\Controllers\Seller\Taobao;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家注销
 */
class ApiControl extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_taobao_app_keyOp() {
        $taobao_app_key = "";
        if(C('taobao_api_isuse')){
            $taobao_app_key = C('taobao_app_key');
        }
        output_data(['taobao_app_key' => $taobao_app_key]);
    }
    
    public function get_taobao_signOp() {
        $taobao_sign = "";
        $taobao_secret_key = C('taobao_secret_key');
        if(C('taobao_api_isuse')){
            $taobao_sign = md5($taobao_secret_key . $_POST['sign_string'] . $taobao_secret_key);
        }
        output_data(['taobao_sign' => $taobao_sign]);
    }
    
    public function get_taobao_session_keyOp() {
        $taobao_session_key = "";
        if(C('taobao_api_isuse')){
            $param = [];
            $param['client_id'] = C('taobao_app_key');
            $param['client_secret'] = C('taobao_secret_key');
            $param['grant_type'] = 'authorization_code';
            $param['code'] = trim($_POST['auth_code']);
            $param['redirect_uri'] = "urn:ietf:wg:oauth:2.0:oob";
            $result = http_post('https://oauth.taobao.com/token', $param);
            if($result){
                $result = json_decode($result);
                if(!empty($result->access_token)){
                    $taobao_session_key = $result->access_token;
                }
            }
        }
        output_data(['taobao_session_key' => $taobao_session_key]);
    }
}
